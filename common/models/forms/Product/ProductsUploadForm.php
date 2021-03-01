<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\models\forms\Product;

use backend\models\Shop\Shop;
use backend\models\Stream\StreamSession;
use common\models\Product\Product;
use Throwable;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * ProductsUploadForm form
 */
class ProductsUploadForm extends Model
{
    const SKU = 'sku';
    const TITLE = 'title';
    const PHOTO = 'photo';
    const LINK = 'link';
    const PRICE = 'price';

    /**
     * Required headers in csv upload
     */
    const REQUIRED_HEADERS = [
        self::SKU,
        self::TITLE,
        self::PHOTO,
        self::LINK,
        self::PRICE,
    ];

    /**
     * Preffix, that determinate option column (lowercase)
     */
    const OPTION_PREFFIX = 'option ';

    /**
     * required fields in options (not dynamic)
     */
    const OPTION_FIELDS = [
        self::PRICE,
    ];

    /**
     * @var UploadedFile|null file attribute
     */
    public $file;

    /** @var Shop */
    protected $shop;

    /**
     * Array of extracted products
     * indexed by sku (to determinate multiple rows with same sku)
     * @var Product[]
     */
    protected $products = [];

    /**
     * @param Shop $shop
     * @param type $config
     */
    public function __construct(Shop $shop, $config = [])
    {
        $this->shop = $shop;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['file', 'required'],
            [
                ['file'],
                'file',
                'skipOnEmpty' => false,
                'extensions' => 'csv',
                'checkExtensionByMimeType' => false,
                'maxFiles' => 1,
                'maxSize' => 1024 * 1024 * 5
            ],
            ['file', 'validateCanUpload']
        ];
    }

    /**
     * Validate upload is available in this shop
     */
    public function validateCanUpload()
    {
        //do not allow upload for active session
        if (StreamSession::activeExists($this->shop->id)) {
            $this->addError('file', Yii::t('app', 'You cannot upload products while Live Stream is active'));
        }
    }

    /**
     * @return bool
     * @throws Throwable
     */
    public function save(): bool
    {
        if (!$this->populateProducts()) {
            return false;
        }
        return $this->saveProducts();
    }

    /**
     * Read CSV, extract and validate header and transform to array
     * populate `products` property
     * phpcs:disable PHPCS_SecurityAudit.BadFunctions
     */
    public function populateProducts(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        if (!$this->file || !($this->file instanceof UploadedFile)) {
            $this->addError('file', 'Incorrect file');
            return false;
        }
        //Extraxct file to array
        $rows = array_map('str_getcsv', file($this->file->tempName));

        //Extract and validate header
        $header = array_map(function ($item) {
            return strtolower(trim($item));
        }, array_shift($rows));
        //Basic Header validation
        $missingHeader = array_diff(self::REQUIRED_HEADERS, $header);
        if ($missingHeader) {
            $this->addError('header', 'Header has missing elements: ' . implode(',', $missingHeader));
            return false;
        }

        //Detect header options
        $headerOptions = self::OPTION_FIELDS;
        foreach ($header as $headerItem) {
            if (strpos($headerItem, self::OPTION_PREFFIX) === 0) {
                $headerOptions[] = $headerItem;
            }
        }

        //Process row by row, populate and validate products
        foreach ($rows as $key => $row) {
            if (count($this->getErrors()) > 50) {
                $this->addError('File', 'The file contains too many errors. Processing terminated.');
                break;
            }
            $this->processRow($row, $key, $header, $headerOptions);
        }
        return !$this->hasErrors();
    }

    /**
     * Process single row and add product to `products` property
     *
     * @param array $row
     * @param string $key
     * @param array $header
     * @param array $headerOptions
     * @return type
     */
    protected function processRow(array $row, string $key, array $header, array $headerOptions)
    {
        if (count($header) !== count($row)) {
            $this->addError($key + 2, 'The string contains an incorrect number of elements.');
            return;
        }
        $productAttributes = array_combine($header, $row);
        foreach ($productAttributes as $field => $value) {
            if (in_array($field, $headerOptions)) {
                $productAttributes['options'][0][$field] = $value;
                unset($productAttributes[$field]);
            }
        }
        $sku = ArrayHelper::getValue($productAttributes, self::SKU);
        if (!array_key_exists($sku, $this->products)) {
            $this->products[$sku] = Product::getOrCreate($this->shop->getId(), $sku);
        } else {
            $this->products[$sku]->addOption($productAttributes['options'][0]); //add option to existing item
            unset($productAttributes['options']);
        }
        $this->products[$sku]->setAttributes($productAttributes);
        $this->products[$sku]->status = Product::STATUS_ACTIVE;
        if (!$this->products[$sku]->validate()) {
            $this->addError($key + 2, implode(' ', $this->products[$sku]->getFirstErrors()));
        }
    }
    /*
     * Save previously populated products
     * @return bool
     * @throws Throwable
     */
    public function saveProducts(): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //Mark products as deleted
            $deleteProductQuery = Product::find()
                ->andWhere(['NOT IN', 'sku', array_keys($this->products)])
                ->andWhere(['<>', 'status', Product::STATUS_DELETED]);
            foreach ($deleteProductQuery->each() as $product) {
                if (!$product->delete()) {
                    $transaction->rollBack();
                    $this->addErrors($product->getErrors());
                    return false;
                }
            }

            //Save new Products
            foreach ($this->products as $product) {
                if (!$product->save()) {
                    $transaction->rollBack();
                    $this->addErrors($product->getErrors());
                    return false;
                }
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return true;
    }

    /**
     * Note: store string number as error key
     * @return type
     */
    public function getErrorsAsString()
    {
        if (!$this->hasErrors()) {
            return '';
        }
        $errors[] = "There are errors in the file. Please correct inconsistencies in line/s and re-upload the file to display the products.";
        foreach ($this->getErrors() as $key => $error) {
            $lineNumber = (is_numeric($key) ? '#' . $key . ': ' : '');
            $errors[] = $lineNumber . implode(' ', $error);
        }
        return implode('<br>', $errors);
    }
}
