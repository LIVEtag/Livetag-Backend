<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\models\forms\Product;

use common\helpers\UploadHelper;
use common\models\Product\Product;
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
    
    /**
     * required field price in option and header
     */
    const PRICE = 'price';
    
    /**
     * required fields in option
     */
    const OPTION_REQUIRED = [
        self::PRICE,
    ];
    
    /**
     * required fields in header
     */
    const HEADER_REQUIRED = [
        self::SKU,
    ];
    
    /**
     * csv header mapping
     */
    const MAP_HEADER_OPTIONS = [
        self::SKU => self::SKU,
        self::TITLE => self::TITLE,
        self::PRICE => self::PRICE,
        self::PHOTO => self::PHOTO,
        self::LINK => self::LINK
    ];
    
    /**
     * @var UploadedFile|null file attribute
     */
    public $file;
    
    /**@var Product $product*/
    private $product;
    
    /**@var array $modelErrors*/
    private $modelErrors;
    
    /**
     * @return array
     */
    public function getModelErrors(): array
    {
        return $this->modelErrors;
    }
    
    /**
     * @param array $modelErrors
     */
    public function setModelErrors(array $modelErrors): void
    {
        $this->modelErrors = $modelErrors;
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->reset();
    }
    
    /**
     * @return void
     */
    public function reset(): void
    {
        $this->product = new Product();
    }
    
    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        $result = $this->product;
        $this->reset();
        return $result;
    }
    
    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
            [
                ['file'],
                'file',
                'skipOnEmpty' => false,
                'extensions' => 'csv',
                'checkExtensionByMimeType'=>false,
                'maxFiles' => 1,
                'maxSize' => 1024*1024*50
            ],
        ];
    }
    
    /**
     * @param string|null $delimiter
     * @return array
     */
    public function convertCsvToArray(string $delimiter = ','): ?array
    {
        $filename = null;
        $data = [];
        $errorsPack = [];
        if ($this->file instanceof UploadedFile) {
            $filename = $this->file->tempName;
        }
        // phpcs:disable PHPCS_SecurityAudit.BadFunctions
        if ($delimiter === null || !file_exists($filename) || !is_readable($filename)) {
            $errorsPack['errors'][] = [
                'line' => 0,
                'error' => 'Incorrect file'
            ];
            return [$data, $errorsPack];
        }
        // phpcs:enable PHPCS_SecurityAudit.BadFunctions
        
        // phpcs:disable PHPCS_SecurityAudit.BadFunctions
        $csvData = file_get_contents($filename);
        // phpcs:enable PHPCS_SecurityAudit.BadFunctions
        $lines = explode(PHP_EOL, $csvData);
        $csvData = array_map('str_getcsv', $lines);
        $firstElement = array_shift($csvData);
        $header = [];
       
        foreach ($firstElement as $headerValue) {
            $header[] = strtolower(trim($headerValue));
        }
        $errorsPack = UploadHelper::validateHeaderCsv($header);
        
        if (!$errorsPack) {
            foreach ($csvData as $keyRow => $row) {
                if ($header && (count($header) === count($row))) {
                    $data[] = array_combine($header, $row);
                } else {
                    $errorsPack['errors'][] = [
                        'line' => $keyRow,
                        'error' => 'Incorrect data of csv file'
                    ];
                }
            }
        }
        
        return [$data, $errorsPack];
    }
    
    
    /**
     * @param array $productItem
     * @param int $shopId
     * @param bool $isProductUploaded
     * @param array $productPack
     * @return void
     */
    private function prepareProductItem(array $productItem, int $shopId, bool $isProductUploaded, array $productPack): void
    {
        $existingProduct = null;
        if (!\array_key_exists($productItem['sku'], $productPack)) {
            //get existing product by sku and shop
            $existingProduct = Product::find()->bySku($productItem['sku'])->byShop($shopId)->one();
        }
        
        /**@var Product $productValue*/
        foreach ($productPack as $productValue) {
            if ($productValue->getAttribute('sku') === $productItem['sku']) {
                $existingProduct = $productValue;
            }
        }
        
        $product = $existingProduct ? : $this->getProduct();
        //set up current product in builder
        $this->setProduct($product);
        //check if product by SKU and shop ID not exists
        $isNewRecord = !$existingProduct;
        //If new upload and product exists
        if (!$isProductUploaded && $existingProduct && !$isNewRecord) {
            $isNewRecord = true;
            $this->product->setAttribute('options', null);
        }
        //get new options
        $options = [];
        foreach ($productItem as $productKey => $productValue) {
            if ($productKey === 'price' || preg_match('/\boption\b/', $productKey)) {
                $options[$productKey] = $productValue;
            }
        }
        
        $this->buildOptionItems($options, $isNewRecord);
        $this->product->setAttributes($productItem);
        
        $this->product->setAttribute('status', Product::STATUS_ACTIVE);
        $this->product->setAttribute('shopId', $shopId);
    }
    
    /**
     * @param array $uploadedDataProducts
     * @param integer $shopId
     * @return void|array
     * @throws \Throwable
     */
    private function prepareStoreProducts(array $uploadedDataProducts, int $shopId): array
    {
        $errorsPack = [];
        $productPack = [];
        
        /**@var array $productItem*/
        foreach ($uploadedDataProducts as $key => $productItem) {
            $skuValue = ArrayHelper::getValue($productItem, 'sku');
            //checking if product is exists while processing new uploading
            $isProductUploaded = isset($productPack[$skuValue]);
            $this->prepareProductItem($productItem, $shopId, $isProductUploaded, $productPack);
            $product = $this->getProduct();
            //get products for save and/or products errors
            [$productPack, $errorsPack] = $this->productValidate($product, $productPack, $key, $errorsPack);
        }
        
        return [$productPack, $errorsPack];
    }
    
    
    /**
     * @param Product $product
     * @param array $productPack
     * @param int $key
     * @param array $errorsPack
     * @return array[]
     */
    private function productValidate(Product $product, array $productPack, int $key, array $errorsPack): array
    {
        if ($product->validate()) {
            //if validated and not yet exists in uploading process
            if (empty($productPack) || !\array_key_exists($product->getAttribute('sku'), $productPack)) {
                $productPack[$product->getAttribute('sku')] = $product;
            }
        } else {
            //if not valid item in file
            foreach ($product->errors as $errors) {
                foreach ($errors as $keyError => $error) {
                    $errorLine = $key + 2;
                    $errorsPack[$keyError][] = [
                        'line' => $errorLine,
                        'error' => "line: {$errorLine} ".
                            ''.
                            ucfirst($error)
                    ];
                }
            }
        }
        
        return [$productPack, $errorsPack];
    }
    
    /**
     * @param array $options
     * @param bool  $isNewRecord
     * @return void
     */
    private function buildOptionItems(array $options, bool &$isNewRecord): void
    {
        foreach ($options as $key => $value) {
            //set up options fields
            if (preg_match('/\boption\b/', $key)) {
                $optionKey = explode(' ', $key);
                if ($optionKey && \is_array($optionKey)) {
                    $key = $optionKey[1];
                }
            }
            
            $getOptions = $this->product->getAttribute('options');
            $decodeOptions = !empty($getOptions) ? $getOptions : [];
            
            if ($decodeOptions) {
                // if new option for existing product then init new item to options
                if (!$isNewRecord) {
                    $decodeOptions[] = [];
                    $isNewRecord = true;
                }
                //start get last element of option and add new key=>value pair to the last element
                $lastKey = array_key_last($decodeOptions);
                $lastOptions = $decodeOptions[$lastKey];
                $lastOptions[$key] = $value;
                //end
                // refresh the last element of options
                $decodeOptions[$lastKey] = $lastOptions;
                $option = $decodeOptions;
            } else {
                //set up new key => value pair for new product's options
                $newOption[$key] = $value;
                $option = [$newOption];
            }
            $this->product->setAttribute('options', $option);
        }
    }
    
    
    /**
     * @param $shopId
     * @return mixed
     * @throws \Throwable
     */
    public function save($shopId)
    {
        //get data from csv file
        [$uploadedDataProducts, $errorsHeaders] = $this->convertCsvToArray();
        
        //check if header of csv file has errors
        if ($errorsHeaders) {
            $errors = UploadHelper::fileErrorUploadResponse($errorsHeaders);
            $this->setModelErrors($errors);
            return false;
        }
    
        [$productPack, $errorsPack] = $this->prepareStoreProducts($uploadedDataProducts, $shopId);
        
        //check if product items has errors
        if ($errorsPack) {
            $errors = UploadHelper::fileErrorUploadResponse($errorsPack);
            $this->setModelErrors($errors);
            return false;
        }
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $isSaved = true;
        try {
            foreach ($productPack as $productToSave) {
                if (!$productToSave->save()) {
                    $isSaved = false;
                    break;
                }
            }
            // set status up delete for existing product which missing in new upload
            if ($isSaved && \is_array($productPack) && $productPack) {
                $uploadedProductIds = array_keys($productPack);
                $this->setProductStatusBySku($uploadedProductIds, Product::STATUS_DELETED);
            }
            
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return true;
    }
    
    /**
     * set status deleted for products existing in database
     * which were not exist while new uploading was processed
     * @param array $uploadedProductIds
     * @param string $status
     */
    private function setProductStatusBySku(array $uploadedProductIds, string $status): void
    {
        // list of product's sku for delete after new upload
        $productUpdate  = Product::find()->where(['NOT IN', 'sku', $uploadedProductIds])->all();
        foreach ($productUpdate as $product) {
            $product->setAttribute('status', $status);
            $product->save();
        }
    }
}
