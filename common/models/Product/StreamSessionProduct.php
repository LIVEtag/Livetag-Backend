<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Product;

use common\components\behaviors\TimestampBehavior;
use common\components\centrifugo\channels\SessionChannel;
use common\components\centrifugo\Message;
use common\components\EventDispatcher;
use common\components\validation\ErrorList;
use common\components\validation\ErrorListInterface;
use common\helpers\LogHelper;
use common\models\Analytics\StreamSessionProductEvent;
use common\models\queries\Product\StreamSessionProductQuery;
use common\models\Stream\StreamSession;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "stream_session_product".
 *
 * @property integer $id
 * @property integer $streamSessionId
 * @property integer $productId
 * @property integer $status
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property-read Product $product
 * @property-read StreamSession $streamSession
 *
 * EVENTS:
 * - EVENT_AFTER_INSERT
 * - EVENT_AFTER_UPDATE
 * - EVENT_AFTER_DELETE
 * @see EventDispatcher
 */
class StreamSessionProduct extends ActiveRecord implements StreamSessionProductInterface
{
    /**
     * Max items in presented now status
     */
    const MAX_PRESENTED_ITEMS = 4;
    const STATUS_DISPLAYED = 2;
    const STATUS_PRESENTED = 3;

    /**
     * Status Names
     */
    const STATUSES = [
        self::STATUS_DISPLAYED => 'In widget',
        self::STATUS_PRESENTED => 'Presented',
    ];

    /**
     * Category for logs
     */
    const LOG_CATEGORY = 'streamSessionProduct';

    /**
     * Product relation key
     */
    const REL_PRODUCT = 'product';

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%stream_session_product}}';
    }

    /**
     * @inheritdoc
     * @return StreamSessionProductQuery the active query used by this AR class.
     */
    public static function find(): StreamSessionProductQuery
    {
        return new StreamSessionProductQuery(get_called_class());
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
            [['streamSessionId', 'productId', 'status'], 'required'],
            [['streamSessionId', 'productId', 'status'], 'integer'],
            [['streamSessionId', 'productId'], 'unique', 'targetAttribute' => ['streamSessionId', 'productId']],
            ['status', 'default', 'value' => self::STATUS_DISPLAYED],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            [['productId'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetRelation' => 'product'],
            [['streamSessionId'], 'exist', 'skipOnError' => true, 'targetClass' => StreamSession::class, 'targetRelation' => 'streamSession'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'streamSessionId' => Yii::t('app', 'Stream session Id'),
            'productId' => Yii::t('app', 'Product Id'),
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
            'productId' => function () {
                return $this->getProductId();
            },
            'status' => function () {
                return $this->getStatus();
            },
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return [
            self::REL_PRODUCT,
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'productId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStreamSession(): ActiveQuery
    {
        return $this->hasOne(StreamSession::class, ['id' => 'streamSessionId']);
    }

    /**
     * Check current session is active
     * @return bool
     */
    public function isDisplayed(): bool
    {
        return $this->getStatus() === self::STATUS_DISPLAYED;
    }

    /**
     * Check current session is stopped
     * @return bool
     */
    public function isPresented(): bool
    {
        return $this->getStatus() === self::STATUS_PRESENTED;
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
    public function getStreamSessionId(): ?int
    {
        return $this->streamSessionId ? (int) $this->streamSessionId : null;
    }

    /**
     * @inheritdoc
     */
    public function getProductId(): ?int
    {
        return $this->productId ? (int) $this->productId : null;
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): ?int
    {
        return $this->status ? (int) $this->status : null;
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
     * Send notification about product to centrifugo
     * @param string $actionType
     */
    public function notify(string $actionType)
    {
        $streamSession = $this->streamSession;
        if ($streamSession && $streamSession->isActive()) {
            $channel = new SessionChannel($this->streamSessionId);
            $message = new Message($actionType, $this->toArray([], [self::REL_PRODUCT]));
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
     * Save product event of active stream session to database
     * @param string $actionType
     */
    public function saveEventToDatabase(string $actionType)
    {
        /** @var StreamSessionProductEvent $event */
        $event = new StreamSessionProductEvent();
        $event->streamSessionId = $this->getStreamSessionId();
        $event->productId = $this->getProductId();
        $event->type = $actionType;
        $event->payload = ['status' => $this->getStatus()];

        if (!$event->save()) {
            LogHelper::error(
                'Failed to save stream session product event',
                self::LOG_CATEGORY,
                LogHelper::extraForModelError($event)
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert): bool
    {
        //Check items with presented status
        if ($this->isAttributeChanged('status') && $this->isPresented() && $this->getCountPresented() >= self::MAX_PRESENTED_ITEMS) {
            $errorList = Yii::createObject(ErrorListInterface::class);
            $error = $errorList->createErrorMessage(ErrorList::CHECKED_TOO_MANY)->setParams(['number' => self::MAX_PRESENTED_ITEMS]);
            $this->addError('status', (string) $error);
            return false;
        }
        return parent::beforeSave($insert);
    }

    /**
     * Get count of presented items in current session
     * @return int
     */
    public function getCountPresented(): int
    {
        return (int) self::find()
                ->byStreamSessionId($this->getStreamSessionId())
                ->byStatus(self::STATUS_PRESENTED)
                ->count();
    }
}
