<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Product;

use common\components\behaviors\TimestampBehavior;
use common\components\centrifugo\channels\ShopChannel;
use common\components\centrifugo\Message;
use common\components\db\BaseActiveRecord;
use common\components\FileSystem\format\FormatEnum;
use common\components\FileSystem\media\MediaTypeEnum;
use common\components\queue\product\ProcessProductImagesJob;
use common\components\validation\validators\ArrayValidator;
use common\components\validation\validators\OptionValidator;
use common\helpers\LogHelper;
use common\models\queries\Product\ProductQuery;
use common\models\Shop\Shop;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "product".
 * @property int $id         [int(10) unsigned]
 * @property string $externalId     [varchar(255)]
 * @property int $shopId     [int(11) unsigned]
 * @property string $title      [varchar(255)]
 * @property string $description [varchar(255)]
 * @property array $options    [json]
 * @property string $photos      [json]
 * @property string $link       [varchar(255)]
 * @property int $status     [tinyint(3)]
 * @property int $createdAt  [int(11) unsigned]
 * @property int $updatedAt  [int(11) unsigned]
 *
 * @property-read ProductMedia[] $productMedias
 */
class Product extends BaseActiveRecord implements ProductInterface
{
    /** @see getProductMedias() */
    const REL_PRODUCT_MEDIA = 'productMedias';

    /**
     * Removed product. required for analytics
     */
    const STATUS_DELETED = 0;

    /**
     * Product created/updated via csv. Photo processing required
     */
    const STATUS_NEW = 2;

    /**
     * The product is sent to queue to process images
     */
    const STATUS_QUEUE = 4;

    /**
     * The product is processing now
     */
    const STATUS_PROCESSING = 6;

    /**
     * The product images could not be processed for some reason
     */
    const STATUS_FAILED = 8;

    /**
     * Default active status
     */
    const STATUS_ACTIVE = 10;

    /**
     * Status Names
     */
    const STATUSES = [
        self::STATUS_DELETED => 'Deleted',
        self::STATUS_NEW => 'New',
        self::STATUS_QUEUE => 'In the queue for processing',
        self::STATUS_PROCESSING => 'Photo processing in progress',
        self::STATUS_FAILED => 'Failed to process photos',
        self::STATUS_ACTIVE => 'Ready',
    ];

    /**
     * required field price in optionOnly files with these types are allowed and headerOnly files with these types are allowed
     */
    const PRICE = 'price';

    /**
     * sku moved to options
     */
    const SKU = 'sku';

    /**
     * external unique id
     */
    const EXTERNAL_ID = 'externalId';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const PHOTOS = 'photos';
    const LINK = 'link';
    const OPTION = 'option';

    /**
     * required fields in option
     */
    const OPTION_REQUIRED = [
        self::PRICE,
        self::SKU,
    ];

    /**
     * Max number of media files
     */
    const MAX_NUMBER_OF_IMAGES = 5;

    /**
     * When seller creates product manually
     */
    const SCENARIO_MANUALLY = 'manually';

    /**
     * media expand
     */
    const EXPAND_MEDIA = 'media';

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%product}}';
    }

    /**
     * @inheritdoc
     * @return ProductQuery the active query used by this AR class.
     */
    public static function find(): ProductQuery
    {
        return new ProductQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['externalId', 'shopId', 'title', 'link', 'options'], 'required'],
            [['photos'], 'required', 'except' => self::SCENARIO_MANUALLY],
            [['shopId', 'status'], 'integer'],
            [['shopId'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::class, 'targetAttribute' => ['shopId' => 'id']],
            [['externalId', 'title', 'link', 'description'], 'string', 'max' => 255],
            ['link', 'url', 'defaultScheme' => 'https'],
            ['photos', ArrayValidator::class, 'max' => self::MAX_NUMBER_OF_IMAGES],
            ['photos', 'each', 'rule' => ['url', 'defaultScheme' => 'https']],
            // externalId and shopId need to be unique together, and they both will receive error message
            [['externalId', 'shopId'], 'unique', 'targetAttribute' => ['externalId', 'shopId']],
            ['options', 'each', 'rule' => [OptionValidator::class]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'externalId' => Yii::t('app', 'External Id'),
            'shopId' => Yii::t('app', 'Shop Id'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'price' => Yii::t('app', 'Price'),
            'photos' => Yii::t('app', 'Photos'),
            'status' => Yii::t('app', 'Status'),
            'createdAt' => Yii::t('app', 'Created at'),
            'updatedAt' => Yii::t('app', 'Updated at'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id' => function () {
                return $this->getId();
            },
            'externalId' => function () {
                return $this->getExternalId();
            },
            'title' => function () {
                return $this->getTitle();
            },
            'description' => function () {
                return $this->getDescription();
            },
            'photo' => function () {
                return $this->getPhoto();
            },
            'link' => function () {
                return $this->getLink();
            },
            'options' => function () {
                return $this->getOptions();
            },
        ];
    }

    /**
     * @see getMedia()
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return [
            self::EXPAND_MEDIA => 'media'
        ];
    }


    /**
     * Return media for selected product (fallback with csv links if no media)
     * @return array
     */
    public function getMedia(): array
    {
        if ($this->productMedias) {
            return $this->productMedias;
        }
        //some kind of fake media
        if ($this->photos) {
            $medias = [];
            foreach ($this->photos as $photo) {
                $medias[] = [
                    'url' => $photo,
                    'type' => MediaTypeEnum::TYPE_IMAGE,
                    'formatted' => array_fill_keys(array_keys(ProductMedia::getFormatters()), $photo)
                ];
            }
            return $medias;
        }
        return [];
    }

    /**
     * @return ActiveQuery
     */
    public function getShop(): ActiveQuery
    {
        return $this->hasOne(Shop::class, ['id' => 'shopId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductMedias(): ActiveQuery
    {
        return $this->hasMany(ProductMedia::class, ['productId' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function getId(): ?int
    {
        return $this->id ? (int) $this->id : null;
    }

    /**
     * @inheritdoc
     */
    public function getExternalId(): ?string
    {
        return $this->externalId ?: null;
    }

    /**
     * @inheritdoc
     */
    public function getShopId(): ?int
    {
        return $this->shopId ? (int) $this->shopId : null;
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): ?string
    {
        return $this->title ?: null;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): ?string
    {
        return $this->description ?: null;
    }

    /**
     * Return first photo. If media exist - return first small photo
     * @inheritdoc
     */
    public function getPhoto(): ?string
    {
        if ($this->productMedias) {
            $media = $this->productMedias[0];
            return $media->getFormattedUrlByName(FormatEnum::SMALL);
        }
        return $this->photos && is_array($this->photos) ? $this->photos[0] : null;
    }

    /**
     * @inheritdoc
     */
    public function getLink(): ?string
    {
        return $this->link ?: null;
    }

    /**
     * @inheritdoc
     */
    public function getOptions(): array
    {
        return $this->options ?: [];
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt ? (int) $this->createdAt : null;
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt(): ?int
    {
        return $this->createdAt ? (int) $this->createdAt : null;
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): ?int
    {
        return isset($this->status) ? (int) $this->status : null;
    }

    /**
     * Check current archive is new
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->getStatus() === self::STATUS_NEW;
    }

    /**
     * Check current archive is send in queue for processing
     * @return bool
     */
    public function isInQueue(): bool
    {
        return $this->getStatus() === self::STATUS_QUEUE;
    }

    /**
     * Set archive status in queue for processing
     */
    public function setInQueue()
    {
        $this->setAttribute('status', self::STATUS_QUEUE);
    }

    /**
     * Check current archive is processing
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->getStatus() === self::STATUS_PROCESSING;
    }

    /**
     * Set archive status processing
     */
    public function setProcessing()
    {
        $this->setAttribute('status', self::STATUS_PROCESSING);
    }

    /**
     * Check current archive is failed
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->getStatus() === self::STATUS_FAILED;
    }

    /**
     * Set archive status failed
     */
    public function setFailed()
    {
        $this->setAttribute('status', self::STATUS_FAILED);
    }

    /**
     * Check current archive is deleted
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->getStatus() === self::STATUS_DELETED;
    }

    /**
     * Check current archive is ready
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getStatus() === self::STATUS_ACTIVE;
    }

    /**
     * Set archive status ready
     */
    public function setActive()
    {
        $this->setAttribute('status', self::STATUS_ACTIVE);
    }

    /**
     * @return string
     */
    public function getStatusName(): ?string
    {
        return ArrayHelper::getValue(self::STATUSES, $this->status);
    }

    /**
     * Fake delete
     */
    public function delete()
    {
        $this->status = self::STATUS_DELETED;
        return $this->save(true, ['status']);
    }

    /**
     * Operation required ONLY when shop deleted.
     */
    public function hardDelete()
    {
        return parent::delete();
    }

    /**
     * Get or Create product by shop and SKU
     * @param int $shopId
     * @param string $externalId
     * @return self|null
     */
    public static function getOrCreate(int $shopId, string $externalId): self
    {
        $product = self::find()->byShop($shopId)->byExternalId($externalId)->one();
        return $product ?? new self(['shopId' => $shopId, self::EXTERNAL_ID => $externalId]);
    }

    /**
     * Add new option to existing one
     * @param array $option
     */
    public function addOption(array $option)
    {
        $options = $this->options;
        $options[] = $option;
        $this->options = array_unique($options, SORT_REGULAR);
    }

    /**
     * Send notification about product to centrifugo (shop channel)
     * @param string $actionType
     */
    public function notify(string $actionType)
    {
        $shop = $this->shop;
        if ($shop) {
            $channel = new ShopChannel($this->shop);
            $message = new Message($actionType, $this->toArray([], [self::EXPAND_MEDIA]));
            if (!Yii::$app->centrifugo->publish($channel, $message)) {
                LogHelper::error('Event Failed', self::LOG_CATEGORY, [
                    'channel' => $channel->getName(),
                    'message' => $message->getBody(),
                    'actionType' => $actionType,
                    'streamSessionProduct' => Json::encode($this->toArray(), JSON_PRETTY_PRINT),
                ]);
            }
        }
    }

    /**
     * Send product to queue for processing
     * Allow only for status "new"
     * @return bool
     */
    public function sendToQueue(): bool
    {
        if (!$this->isNew()) {
            return false;
        }

        //Create job to process images
        $job = new ProcessProductImagesJob();
        $job->id = $this->id;
        Yii::$app->queueProduct->push($job);

        //set in queue
        $this->setInQueue();
        return $this->save(false, ['status', 'updatedAt']);
    }
}
