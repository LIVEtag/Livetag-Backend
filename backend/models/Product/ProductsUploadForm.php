<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\models\Product;

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
    const ID = 'id';

    /**
     * Mapping fields from csv with existing fields
     */
    const HEADER_MAPPING = [
        self::ID => Product::EXTERNAL_ID
    ];

    /**
     * Required headers in csv upload
     */
    const REQUIRED_HEADERS = [
        Product::EXTERNAL_ID, //@see HEADER_MAPPING
        Product::SKU,
        Product::TITLE,
        Product::PHOTO,
        Product::LINK,
        Product::PRICE,
    ];

    /**
     * Preffix, that determinate option column (lowercase)
     */
    const OPTION_PREFFIX = 'option ';

    /**
     * required fields in options (not dynamic)
     */
    const OPTION_FIELDS = [
        Product::SKU,
        Product::PRICE,
    ];

    /**
     * @var UploadedFile|null file attribute
     */
    public $file;

    /** @var Shop */
    protected $shop;

    /**
     * Array of extracted products
     * indexed by externalId (to determinate multiple rows with same id)
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
        if (StreamSession::activeExists($this->shop->getId())) {
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
            $item = strtolower(trim($item));
            return self::HEADER_MAPPING[$item] ?? $item;
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
        $id = ArrayHelper::getValue($productAttributes, Product::EXTERNAL_ID);
        if (!array_key_exists($id, $this->products)) {
            $this->products[$id] = Product::getOrCreate($this->shop->getId(), $id);
        } else {
            $this->products[$id]->addOption($productAttributes['options'][0]); //add option to existing item
            unset($productAttributes['options']);
        }
        $this->products[$id]->setAttributes($productAttributes);
        $this->products[$id]->status = Product::STATUS_ACTIVE;
        if (!$this->products[$id]->validate()) {
            $this->addError($key + 2, implode(' ', $this->products[$id]->getFirstErrors()));
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
                ->andWhere(['shopId' => $this->shop->getId()])
                ->andWhere(['NOT IN', Product::EXTERNAL_ID, array_keys($this->products)])
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
