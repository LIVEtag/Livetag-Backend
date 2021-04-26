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
use common\components\db\BaseActiveRecord;
use common\components\EventDispatcher;
use common\components\validation\ErrorList;
use common\components\validation\ErrorListInterface;
use common\exception\AfterSaveException;
use common\helpers\LogHelper;
use common\models\Analytics\StreamSessionEvent;
use common\models\Analytics\StreamSessionProductEvent;
use common\models\Analytics\StreamSessionStatistic;
use common\models\Comment\Comment;
use common\models\Product\Product;
use common\models\Product\StreamSessionProduct;
use common\models\queries\Analytics\StreamSessionStatisticQuery;
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
use yii\db\Expression;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * This is the model class for table "stream_session".
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @todo: The class StreamSession has an overall complexity of 69 which is very high. The configured complexity threshold is 65.
 * @todo: The class StreamSession has 21 public methods. Consider refactoring StreamSession to keep number of public methods under 20.
 * @todo: Rename sessionId -> externalId
 *
 * @property integer $id
 * @property string $name
 * @property integer $shopId
 * @property integer $status
 * @property boolean $commentsEnabled
 * @property string $sessionId
 * @property integer $createdAt
 * @property integer $announcedAt
 * @property integer $duration
 * @property integer $startedAt
 * @property integer $stoppedAt
 * @property boolean $isPublished
 * @property string $rotate
 *
 * @property-read Comment[] $comments
 * @property-read Shop $shop
 * @property-read StreamSessionCover $streamSessionCover
 * @property-read StreamSessionArchive $archive
 * @property-read StreamSessionToken $streamSessionToken
 * @property-read StreamSessionProduct[] $streamSessionProducts
 * @property-read Product[] $products
 * @property-read StreamSessionEvent[] $streamSessionEvents
 * @property-read StreamSessionProductEvent[] $streamSessionProductEvents
 * @property-read StreamSessionStatistic $streamSessionStatistic
 *
 * EVENTS:
 * - EVENT_AFTER_COMMIT_INSERT
 * - EVENT_AFTER_COMMIT_UPDATE
 * - EVENT_END_SOON
 * - EVENT_SUBSCRIBER_TOKEN_CREATED
 * @see EventDispatcher
 */
class StreamSession extends BaseActiveRecord implements StreamSessionInterface
{
    /** @see getShop() */
    const REL_SHOP = 'shop';

    /** @see getStreamSessionCover() */
    const REL_STREAM_SESSION_COVER = 'streamSessionCover';

    /** @see getArchive() */
    const REL_ARCHIVE = 'archive';

    /** @see getProducts() */
    const REL_PRODUCT = 'products';

    /** @see getStreamSessionProducts() */
    const REL_STREAM_SESSION_PRODUCT = 'streamSessionProducts';

    /** @see getComments() */
    const REL_COMMENT = 'comments';

    /** @see getStreamSessionTokens() */
    const REL_STREAM_SESSION_TOKEN = 'streamSessionTokens';

    /** @see getStreamSessionEvents() */
    const REL_STREAM_SESSION_EVENT = 'streamSessionEvents';

    /** @see getStreamSessionProductEvents() */
    const REL_STREAM_SESSION_PRODUCT_EVENT = 'streamSessionProductEvents';

    /** @see getStreamSessionStatistic() */
    const REL_STREAM_SESSION_STATISTIC = 'streamSessionStatistic';

    /**
     * When my livestream has a duration of 2 h 50m. Then I want to get a LivestreamEnd10Min notification
     */
    const EVENT_END_SOON = 'endSoon';

    /**
     * Date.TooBig (366 days after today)
     */
    const MAX_ANNOUNCED_AT_DAYS = 366;

    /**
     * Created vonage token for subscriber
     */
    const EVENT_SUBSCRIBER_TOKEN_CREATED = 'subscriberTokenCreated';
    const DURATION_30 = 1800;
    const DURATION_60 = 3600;
    const DURATION_90 = 5400;
    const DURATION_120 = 7200;
    const DURATION_150 = 9000;
    const DURATION_180 = 10800;

    /**
     * Available durations
     */
    const DURATIONS = [
        self::DURATION_30 => '30m',
        self::DURATION_60 => '1h',
        self::DURATION_90 => '1h 30m',
        self::DURATION_120 => '2h',
        self::DURATION_150 => '2h 30m',
        self::DURATION_180 => '3h',
    ];

    /**
     * Default Session lifetime (3 hours)
     */
    const DEFAULT_DURATION = self::DURATION_180;

    /**
     * maximum name length
     */
    const MAX_NAME_LENGTH = 55;

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
     * Archived Translation
     */
    const STATUS_ARCHIVED = 4;

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
        self::STATUS_ARCHIVED => 'Archived',
    ];

    const ROTATE_0 = '0';
    const ROTATE_90 = '90';
    const ROTATE_180 = '180';
    const ROTATE_270 = '270';

    const ROTATIONS = [
        self::ROTATE_0 => '0 degrees',
        self::ROTATE_90 => '90 degrees',
        self::ROTATE_180 => '180 degrees',
        self::ROTATE_270 => '270 degrees',
    ];

    /**
     * Stream session with uploaded show will have empty announcedAt, startedAt and stoppedAt
     */
    const SCENARIO_UPLOAD_SHOW = 'upload-show';

    /**
     * Array of Product ids to link
     *
     * null represent, that field not extracted and processed
     * empty array means that there is no releated products
     *
     * @var array|null
     */
    protected $productIds = null;

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
     * @inherritdoc
     */
    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL
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
            [['shopId'], 'required'],
            [['announcedAt'], 'required', 'except' => self::SCENARIO_UPLOAD_SHOW],
            [['shopId', 'status', 'startedAt', 'stoppedAt', 'announcedAt'], 'integer'],
            ['sessionId', 'string', 'max' => 255],
            ['name', 'default', 'value' => ''],
            ['name', 'string', 'max' => self::MAX_NAME_LENGTH],
            ['shopId', 'exist', 'skipOnError' => true, 'targetRelation' => 'shop'],
            [['commentsEnabled', 'isPublished'], 'default', 'value' => true],
            [['commentsEnabled', 'isPublished'], 'boolean'],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            ['duration', 'default', 'value' => self::DEFAULT_DURATION],
            ['duration', 'in', 'range' => array_keys(self::DURATIONS)],
            ['rotate', 'default', 'value' => self::ROTATE_0],
            ['rotate', 'in', 'range' => array_keys(self::ROTATIONS)],
            [
                'announcedAt',
                'validateLimits',
                'when' => function ($model) {
                    return $model->isNew();
                }
            ],
            [
                'announcedAt',
                'validateBusyTime',
                'when' => function ($model) {
                    return $model->isNew();
                }
            ],
            [
                'startedAt',
                'required',
                'when' => function (self $model) {
                    return $model->isActive();
                },
                'except' => self::SCENARIO_UPLOAD_SHOW,
            ],
            [
                'stoppedAt',
                'required',
                'when' => function (self $model) {
                    return $model->isStopped();
                },
                'except' => self::SCENARIO_UPLOAD_SHOW,
            ],
            [
                'productIds',
                'each',
                'rule' => [
                    'exist',
                    'targetClass' => Product::class,
                    'targetAttribute' => 'id',
                    'filter' => function (ProductQuery $query) {
                        $query->byShop($this->getShopId());
                    }
                ],
            ],
        ];
    }

    /**
     * Validate (>now)  && (<366 days after today)
     * @param string $attribute
     */
    public function validateLimits($attribute)
    {
        $now = time();
        if ($this->$attribute > ($now + self::MAX_ANNOUNCED_AT_DAYS * 24 * 60 * 60)) {
            $errorList = Yii::createObject(ErrorListInterface::class);
            $this->addError(
                $attribute,
                $errorList->createErrorMessage(ErrorList::DATE_TOO_BIG)
                    ->setParams([
                        'attribute' => $this->getAttributeLabel($attribute),
                        'max' => self::MAX_ANNOUNCED_AT_DAYS
                    ])
            );
        } elseif ($this->$attribute < $now) {
            $errorList = Yii::createObject(ErrorListInterface::class);
            $this->addError(
                $attribute,
                $errorList->createErrorMessage(ErrorList::DATE_TOO_SMALL)
                    ->setParams([
                        'attribute' => $this->getAttributeLabel($attribute),
                        'min' => 'now'
                    ])
            );
        }
    }

    /**
     * Validate crosses with other streams
     * @see https://helgesverre.com/blog/mysql-overlapping-intersecting-dates/
     * @param $attribute
     */
    public function validateBusyTime($attribute)
    {
        //do not validate if other validation failed
        //'shopId' and 'announcedAt' required for this validation
        if ($this->hasErrors()) {
            return;
        }

        $startTime = $this->$attribute;
        $endTime = $startTime + $this->getDuration();

        $query = self::find()
            ->byShopId($this->getShopId())
            ->select(['announcedAt', new Expression('announcedAt + duration as expiredAt')])
            ->byStatus([self::STATUS_NEW, self::STATUS_ACTIVE])
            ->andHaving([
                'OR',
                [
                    'AND',
                    ['<', 'announcedAt', $endTime],
                    ['>', 'expiredAt', $startTime],
                ],
                [
                    'AND',
                    ['>', 'announcedAt', $endTime],
                    ['<', 'announcedAt', $startTime],
                    ['<', 'expiredAt', $startTime],
                ],
                [
                    'AND',
                    ['<', 'expiredAt', $startTime],
                    ['>', 'expiredAt', $endTime],
                    ['<', 'announcedAt', $endTime],
                ],
                [
                    'AND',
                    ['>', 'announcedAt', $endTime],
                    ['<', 'announcedAt', $startTime],
                ]
            ]);

        $id = $this->getId();
        if ($id) {
            $query->andWhere(['<>', 'id', $id]); // allow to update model to itself - compare with another intervals
        }

        if ($query->exists()) {
            $errorList = Yii::createObject(ErrorListInterface::class);
            $this->addError($attribute, $errorList->createErrorMessage(ErrorList::BUSY_TIME));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'shopId' => Yii::t('app', 'Shop ID'),
            'status' => Yii::t('app', 'Status'),
            'commentsEnabled' => Yii::t('app', 'Comments Enabled'),
            'sessionId' => Yii::t('app', 'Session ID'),
            'createdAt' => Yii::t('app', 'Created At'),
            'announcedAt' => Yii::t('app', 'Start At'),
            'duration' => Yii::t('app', 'Duration'),
            'startedAt' => Yii::t('app', 'Started At'),
            'stoppedAt' => Yii::t('app', 'Stopped At'),
            'isPublished' => Yii::t('app', 'Is Published'),
            'rotate' => Yii::t('app', 'Rotate'),
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
            'name',
            'cover' => 'streamSessionCover',
            'shopUri' => function () {
                return $this->shop->uri;
            },
            'sessionId',
            'status' => function () {
                return $this->getStatus();
            },
            'commentsEnabled' => function () {
                return $this->getCommentsEnabled();
            },
            'createdAt' => function () {
                return $this->getCreatedAt();
            },
            'announcedAt' => function () {
                return $this->getAnnouncedAt();
            },
            'duration' => function () {
                return $this->getDuration();
            },
            'startedAt' => function () {
                return $this->getStartedAt();
            },
            'stoppedAt' => function () {
                return $this->getStoppedAt();
            },
            'rotate' => function () {
                return $this->getRotate();
            }
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return [
            self::REL_ARCHIVE,
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
    public function getStreamSessionCover(): ActiveQuery
    {
        return $this->hasOne(StreamSessionCover::class, ['streamSessionId' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getArchive(): ActiveQuery
    {
        return $this->hasOne(StreamSessionArchive::class, ['streamSessionId' => 'id']);
    }

    /**
     * @return string|null
     */
    public function getCoverUrl(): ?string
    {
        $cover = $this->streamSessionCover;
        return $cover ? $cover->getUrl() : null;
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
     * Allow unset products (if empty array came).
     * If productIds is null - no action required
     * @return array|null
     */
    public function getProductIds(): ?array
    {
        if ($this->productIds === null) {
            $this->productIds = $this->getStreamSessionProducts()->select('productId')->column();
        }
        return $this->productIds;
    }

    /**
     * @param array $ids
     */
    public function setProductIds($ids)
    {
        $this->productIds = $ids;
    }

    /**
     * @return StreamSessionStatisticQuery
     */
    public function getStreamSessionStatistic(): StreamSessionStatisticQuery
    {
        return $this->hasOne(StreamSessionStatistic::class, ['streamSessionId' => 'id']);
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
    public function getName(): string
    {
        return $this->name;
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
    public function getCommentsEnabled(): bool
    {
        return (bool) $this->commentsEnabled;
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
    public function getAnnouncedAt(): ?int
    {
        return $this->announcedAt ? (int) $this->announcedAt : null;
    }

    /**
     * @inheritdoc
     */
    public function getDuration(): ?int
    {
        return $this->duration ? (int) $this->duration : null;
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
        return $this->getAnnouncedAt() && $this->getDuration() ? $this->getAnnouncedAt() + $this->getDuration() : null;
    }

    /**
     * @return int
     */
    public function getRotate(): int
    {
        return (int)$this->rotate;
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
     * Check current session is archived
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->getStatus() === self::STATUS_ARCHIVED;
    }

    /**
     * Check current session is active
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getStatus() === self::STATUS_ACTIVE;
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
     * Check user can add comment to current stream
     * @param User $user
     * @throws ForbiddenHttpException
     */
    public function checkCanAddComment(User $user)
    {
        if (!$this->isActive()) {
            throw new ForbiddenHttpException('Commenting is available only for the active livestream');
        }
        if (!$this->getCommentsEnabled()) {
            throw new ForbiddenHttpException('Comment section of the widget was disabled');
        }
        //Do not allow seller from another shop post a comment
        if ($user->isSeller && (!$user->shop || $user->shop->id !== $this->shopId)) {
            throw new ForbiddenHttpException('You can not leave comments in non-your broadcast.');
        } elseif ($user->isBuyer && !$user->name) {
            throw new ForbiddenHttpException('You cannot leave a comment without specifying a name.');
        }
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
        $subscriberToken = $this->createSubscriberToken();
        $this->trigger(StreamSession::EVENT_SUBSCRIBER_TOKEN_CREATED, new StreamSessionSubscriberTokenCreatedEvent($user));
        return $subscriberToken;
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
        return self::find()->byShopId($shopId)->active()->published()->orderByLatest()->one();
    }

    /**
     * Check active session exist for selected shop
     *
     * @param int $shopId
     * @return self|null
     */
    public static function activeExists($shopId): bool
    {
        return self::find()->byShopId($shopId)->active()->exists();
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
            throw new UnprocessableEntityHttpException(ErrorList::errorTextByCode(ErrorList::STREAM_IN_PROGRESS)); //!
        }

        $session = new self([
            'shopId' => $shop->id,
            'status' => self::STATUS_NEW,
            'announcedAt' => time() + 60, //start in minute to avoid "<now" validation
        ]);

        $session->save();
        return $session; //return model or model with erros
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
     * Publish stream
     * @return bool
     */
    public function publish(): bool
    {
        if ($this->isPublished) {
            return true;
        }
        $this->isPublished = true;
        return $this->save(true, ['isPublished']);
    }

    /**
     * Unpublish stream
     * @return bool
     * @throws BadRequestHttpException
     */
    public function unpublish(): bool
    {
        if ($this->isActive()) {
            throw new BadRequestHttpException('You can\'t unpublish active translations');
        }
        if (!$this->isPublished) {
            return true;
        }
        $this->isPublished = false;
        return $this->save(true, ['isPublished']);
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
     * Save product events of active stream session to database
     */
    public function saveProductEventsToDatabase()
    {
        $streamSessionProducts = $this->streamSessionProducts;
        foreach ($streamSessionProducts as $streamSessionProduct) {
            $streamSessionProduct->saveEventToDatabase(StreamSessionProductEvent::TYPE_PRODUCT_CREATE);
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        // Create session with tokbox
        if ($insert && !$this->sessionId) {
            try {
                $this->sessionId = Yii::$app->vonage->createSession();
            } catch (Throwable $ex) {
                $this->addError('sessionId', $ex->getMessage());
                return false;
            }
        }
        return parent::beforeSave($insert);
    }

    /**
     * Scenario with transaction rollback, if error in afterSave catch -  thrown exception
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @param boolean $runValidation
     * @param array $attributeNames
     * @return boolean
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        try {
            return parent::save($runValidation, $attributeNames);
        } catch (AfterSaveException $ex) {
            return false;
        }
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws AfterSaveException
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->saveRelations();
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Save relation tables
     * Each related table should add errors to main model if save failed
     *
     * @throws AfterSaveException
     */
    public function saveRelations()
    {
        if ($this->productIds !== null) {
            $this->linkProducts();
        }
        if ($this->hasErrors()) {
            throw new AfterSaveException();
        }
    }

    /**
     * If product ID not exist - this ID will be ignoring.
     * If product ID is duplicate - only one relation will be save.
     */
    protected function linkProducts()
    {
        //sorts by value: from smallest to largest
        sort($this->productIds);
        $existProductIds = $this->getStreamSessionProducts()->select('productId')->orderBy('productId')->column();

        //remove old
        $productsIdsToRemove = array_diff($existProductIds, $this->productIds);
        if ($productsIdsToRemove) {
            $removeQuery = $this->getStreamSessionProducts()->andWhere(['productId' => $productsIdsToRemove]);
            foreach ($removeQuery->each() as $streamSessionProduct) {
                if (!$streamSessionProduct->delete()) {
                    $this->addError(
                        'productIds',
                        Yii::t('app', 'Failed to unlink product {productId}', ['productId' => $streamSessionProduct->productId])
                    );
                }
            }
        }

        //create new
        $newProductIds = array_diff($this->productIds, $existProductIds);
        foreach ($newProductIds as $productId) {
            $model = new StreamSessionProduct([
                'streamSessionId' => $this->id,
                'productId' => $productId,
                'status' => StreamSessionProduct::STATUS_DISPLAYED
            ]);
            if (!$model->save()) {
                $this->addError('productIds', implode(', ', $model->getFirstErrors()));
                return;
            }
        }
    }
}
