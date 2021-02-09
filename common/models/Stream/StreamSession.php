<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Stream;

use common\components\behaviors\TimestampBehavior;
use common\components\centrifugo\channels\ShopChannel;
use common\components\centrifugo\Message;
use common\components\EventDispatcher;
use common\components\validation\ErrorList;
use common\helpers\LogHelper;
use common\models\Comment\Comment;
use common\models\Product\Product;
use common\models\Product\StreamSessionProduct;
use common\models\queries\Comment\CommentQuery;
use common\models\queries\Product\ProductQuery;
use common\models\queries\Product\StreamSessionProductQuery;
use common\models\queries\Stream\StreamSessionQuery;
use common\models\Shop\Shop;
use common\models\User;
use OpenTok\Role;
use Throwable;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * This is the model class for table "stream_session".
 *
 * @property integer $id
 * @property integer $shopId
 * @property integer $status
 * @property string $sessionId
 * @property integer $createdAt
 * @property integer $startedAt
 * @property integer $stoppedAt
 *
 * @property-read Comment[] $comments
 * @property-read Shop $shop
 * @property-read StreamSessionToken $streamSessionToken
 * @property-read StreamSessionProduct[] $streamSessionProducts
 * @property-read Product[] $products
 *
 * EVENTS:
 * - EVENT_AFTER_INSERT
 * - EVENT_AFTER_UPDATE
 * - EVENT_END_SOON
 * @see EventDispatcher
 */
class StreamSession extends ActiveRecord implements StreamSessionInterface
{
    /**
     * When my livestream has a duration of 2 h 50m. Then I want to get a LivestreamEnd10Min notification
     */
    const EVENT_END_SOON = 'endSoon';

    /**
     * Default Session lifetime (3 hours)
     */
    const DEFAULT_DURATION = 10800;

    /**
     * Default status afrer creation
     */
    const STATUS_NEW = 1;

    /**
     * Session in progress
     */
    const STATUS_ACTIVE = 2;

    /**
     * Translation finished (stop method used) or stopped by cron
     */
    const STATUS_STOPPED = 3;

    /**
     * Category for logs
     */
    const LOG_CATEGORY = 'streamSession';

    /**
     * Status Names
     */
    const STATUSES = [
        self::STATUS_NEW => 'New',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_STOPPED => 'Stopped',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%stream_session}}';
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     * @return StreamSessionQuery the active query used by this AR class.
     */
    public static function find(): StreamSessionQuery
    {
        return new StreamSessionQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['shopId', 'sessionId'], 'required'],
            [['shopId', 'status', 'startedAt', 'stoppedAt'], 'integer'],
            ['sessionId', 'string', 'max' => 255],
            ['shopId', 'exist', 'skipOnError' => true, 'targetRelation' => 'shop'],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            [
                'startedAt',
                'required',
                'when' => function (self $model) {
                    return $model->isActive();
                }
            ],
            [
                'stoppedAt',
                'required',
                'when' => function (self $model) {
                    return $model->isStopped();
                }
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'shopId' => Yii::t('app', 'Shop ID'),
            'status' => Yii::t('app', 'Status'),
            'sessionId' => Yii::t('app', 'Session ID'),
            'expiredAt' => Yii::t('app', 'Expired At'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id' => function () {
                return $this->getId();
            },
            'shopUri' => function () {
                return $this->shop->uri;
            },
            'sessionId',
            'status' => function () {
                return $this->getStatus();
            },
            'createdAt' => function () {
                return $this->getCreatedAt();
            },
            'startedAt' => function () {
                return $this->getStartedAt();
            },
            'stoppedAt' => function () {
                return $this->getStoppedAt();
            },
        ];
    }

    /**
     * @return CommentQuery
     */
    public function getComments(): CommentQuery
    {
        return $this->hasMany(Comment::class, ['streamSessionId' => 'id']);
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
    public function getStreamSessionToken(): ActiveQuery
    {
        return $this->hasOne(StreamSessionToken::class, ['streamSessionId' => 'id']);
    }

    /**
     * @return StreamSessionProductQuery
     */
    public function getStreamSessionProducts(): StreamSessionProductQuery
    {
        return $this->hasMany(StreamSessionProduct::class, ['streamSessionId' => 'id']);
    }

    /**
     * @return ProductQuery
     */
    public function getProducts(): ProductQuery
    {
        return $this->hasMany(Product::class, ['id' => 'productId'])->via('streamSessionProducts');
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
    public function getShopId(): ?int
    {
        return $this->shopId ? (int) $this->shopId : null;
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
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @inheritdoc
     */
    public function getPublisherToken(): ?StreamSessionToken
    {
        return $this->streamSessionToken;
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
    public function getStartedAt(): ?int
    {
        return $this->startedAt ? (int) $this->startedAt : null;
    }

    /**
     * @inheritdoc
     */
    public function getStoppedAt(): ?int
    {
        return $this->stoppedAt ? (int) $this->stoppedAt : null;
    }

    /**
     * @inheritdoc
     */
    public function getExpiredAt(): ?int
    {
        return $this->getStartedAt() ? $this->getStartedAt() + self::DEFAULT_DURATION : null;
    }

    /**
     * Check current session is new (announcement)
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->getStatus() === self::STATUS_NEW;
    }

    /**
     * Check current session is active
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getStatus() === self::STATUS_ACTIVE; // && $this->getExpiredAt() && $this->getExpiredAt() > time();
    }

    /**
     * Check current session is stopped
     * @return bool
     */
    public function isStopped(): bool
    {
        return $this->getStatus() === self::STATUS_STOPPED;
    }

    /**
     * Get token for selected user (only for active sessions):
     *  - get publisher token for owner
     *  - generate token for other users or guests
     * @param User|null $user
     * @return StreamSessionToken|null
     */
    public function getToken(?User $user = null): ?StreamSessionToken
    {
        if (!$this->isActive()) {
            throw new BadRequestHttpException('You can only get a token for active translation');
        }

        if ($user && $this->getIsOwner($user)) {
            return $this->getPublisherToken();
        }
        return $this->createSubscriberToken();
    }

    /**
     * Check selected user is owner of session (belongs to session shop)
     * @param User $user
     * @return bool
     */
    public function getIsOwner(User $user): bool
    {
        if ($user->shop && $this->shopId && $user->shop->id === $this->shopId) {
            return true;
        }
        return false;
    }

    /**
     * Create token for stream subscriber
     * @return StreamSessionToken
     */
    public function createSubscriberToken(): StreamSessionToken
    {
        return $this->createToken(Role::SUBSCRIBER);
    }

    /**
     * Create token for stream publisher
     * @return StreamSessionToken
     */
    public function createPublisherToken(): StreamSessionToken
    {
        return $this->createToken(Role::MODERATOR);
    }

    /**
     *
     * @param string $role
     * @return StreamSessionToken
     * @throws UnprocessableEntityHttpException
     */
    public function createToken($role): StreamSessionToken
    {
        if (!$this->getExpiredAt() || !$this->getSessionId()) {
            throw new UnprocessableEntityHttpException('Failed to create token');
        }

        $token = Yii::$app->vonage->getToken($this->getSessionId(), [
            'role' => $role,
            'expireTime' => $this->getExpiredAt()
        ]);

        return new StreamSessionToken([
            'streamSessionId' => $this->id,
            'token' => $token,
            'expiredAt' => $this->getExpiredAt(),
        ]);
    }

    /**
     * Get current Active session for selected shop(by id)
     *
     * @param int $shopId
     * @return self|null
     */
    public static function getCurrent($shopId): ?self
    {
        return self::find()->byShopId($shopId)->active()->orderByLatest()->one();
    }

    /**
     * Create translation for selected shop
     * Do not allow create new session if active exist
     *
     * Create session for specified user.
     * At the moment, a user can only belong to one shop.
     * Accordingly, we can say that if a user tries to start a stream, he starts only within his store.
     * If there is no store, the user will not be able to start the stream
     *
     * OpenTok sessions do not expire. However, authentication tokens do expire (see the generateToken() method).
     * Also note that sessions cannot explicitly be destroyed.
     *
     * @param Shop $shop
     * @return self (return created model or model with errors)
     * @throws UnprocessableEntityHttpException
     */
    public static function create(Shop $shop): self
    {
        $currentSession = self::getCurrent($shop->id);
        if ($currentSession) {
            throw new UnprocessableEntityHttpException(ErrorList::errorTextByCode(ErrorList::STREAM_IN_PROGRESS));
        }

        $session = new self([
            'shopId' => $shop->id,
            'status' => self::STATUS_NEW
        ]);

        //1. Validate shop for broadcast start (check is active)
        if (!$session->validate(['shopId', 'status'])) {
            return $session; //return model errors
        }

        // 2. Create session with tokbox
        try {
            $session->sessionId = Yii::$app->vonage->createSession();
        } catch (Throwable $ex) {
            $session->addError('sessionId', $ex->getMessage());
            return $session; //return model with errors
        }

        // 4. Store session into DB
        $session->save();
        return $session; //return model or model with erros
    }

    /**
     * Start translation and return token
     * @return StreamSessionToken
     * @throws BadRequestHttpException
     */
    public function start(): StreamSessionToken
    {
        if (!$this->isNew()) {
            throw new BadRequestHttpException('This translation already started');
        }
        // 1. Touch startedAt to generate expired time for token
        $this->touch('startedAt');

        // 2. Create publisher token
        $token = $this->createPublisherToken();

        //Save token and update session status
        $transaction = Yii::$app->db->beginTransaction();

        // 3. Save token
        if (!$token->save()) {
            $transaction->rollBack();
            LogHelper::error('Session start failed. Session Token not saved', self::LOG_CATEGORY, LogHelper::extraForModelError($token));
            throw new BadRequestHttpException(Yii::t('app', 'Failed to start session for unknown reason'));
        }

        // 4. Update status and save session
        $this->status = self::STATUS_ACTIVE;
        if (!$this->save(true, ['status', 'startedAt'])) {
            $transaction->rollBack();
            LogHelper::error('Session start failed. Session not saved', self::LOG_CATEGORY, LogHelper::extraForModelError($this));
            throw new BadRequestHttpException(Yii::t('app', 'Failed to start session for unknown reason'));
        }
        $transaction->commit();
        return $token;
    }

    /**
     * Stop translation
     * @return bool
     * @throws BadRequestHttpException
     */
    public function stop(): bool
    {
        if (!$this->isActive()) {
            throw new BadRequestHttpException('You can only stop active translations');
        }
        $this->status = self::STATUS_STOPPED;
        $this->touch('stoppedAt');
        return $this->save(true, ['status', 'stoppedAt']);
    }

    /**
     * Send notification about session to centrifugo
     * @param string $actionType
     */
    public function notify(string $actionType)
    {
        $channel = new ShopChannel($this->shop);
        $message = new Message($actionType, $this->toArray());
        if (!Yii::$app->centrifugo->publish($channel, $message)) {
            LogHelper::error('Event Failed', self::LOG_CATEGORY, [
                'channel' => $channel->getName(),
                'message' => $message->getBody(),
                'actionType' => $actionType,
                'streamSession' => Json::encode($this->toArray(), JSON_PRETTY_PRINT),
            ]);
        }
    }

    /**
     * Link all existing Products to current Stream Session
     */
    public function linkProducts()
    {
        $productQuery = Product::find()->byShop($this->shopId)->active();
        foreach ($productQuery->each() as $product) {
            try {
                $this->link('products', $product, ['status' => StreamSessionProduct::STATUS_DISPLAYED]);
            } catch (Throwable $ex) {
                LogHelper::error(
                    'Failed to link Product to Stream Session',
                    self::LOG_CATEGORY,
                    LogHelper::extraForException($product, $ex)
                );
            }
        }
    }
}
