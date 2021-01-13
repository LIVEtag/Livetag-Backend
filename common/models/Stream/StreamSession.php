<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Stream;

use common\components\behaviors\TimestampBehavior;
use common\components\validation\ErrorList;
use common\models\queries\Stream\StreamSessionQuery;
use common\models\Shop\Shop;
use common\models\User;
use OpenTok\Role;
use Throwable;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\UnprocessableEntityHttpException;

/**
 * This is the model class for table "stream_session".
 *
 * @property integer $id
 * @property integer $shopId
 * @property integer $status
 * @property string $sessionId
 * @property string $publisherToken
 * @property integer $expiredAt
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property-read Shop $shop
 */
class StreamSession extends ActiveRecord implements StreamSessionInterface
{
    /**
     * Default Session lifetime (3 hours)
     */
    const DEFAULT_DURATION = 10800;

    /**
     * Disabled shop (marked as deleted)
     */
    const STATUS_STOPPED = 0;

    /**
     * Default active shop
     */
    const STATUS_ACTIVE = 10;

    /**
     * Status Names
     */
    const STATUSES = [
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
            TimestampBehavior::class,
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
            [['shopId', 'sessionId', 'publisherToken', 'expiredAt'], 'required'],
            [['shopId', 'status', 'expiredAt'], 'integer'],
            ['sessionId', 'string', 'max' => 255],
            ['publisherToken', 'string', 'max' => 512],
            [
                'shopId',
                'exist',
                'skipOnError' => true,
                'targetRelation' => 'shop',
                'filter' => function ($query) {
                    $query->active(); //only active shop can start sessions
                },
            ],
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
            'id' => Yii::t('app', 'ID'),
            'shopId' => Yii::t('app', 'Shop ID'),
            'status' => Yii::t('app', 'Status'),
            'sessionId' => Yii::t('app', 'Session ID'),
            'publisherToken' => Yii::t('app', 'Publisher Token'),
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
            'shopId' => function () {
                return $this->getShopId();
            },
            'sessionId',
            'isActive' => function () {
                return $this->isActive();
            },
            'token' => function () {
                return $this->getToken(Yii::$app->user->identity ?? null);
            },
            'createdAt' => function () {
                return $this->getCreatedAt();
            },
            'expiredAt' => function () {
                return $this->getExpiredAt();
            },
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getShop(): ActiveQuery
    {
        return $this->hasOne(Shop::class, ['id' => 'shopId']);
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
    public function getPublisherToken(): string
    {
        return $this->publisherToken;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt ? (int) $this->createdAt : null;
    }

    /**
     * @inheritdoc
     */
    public function getExpiredAt(): int
    {
        return $this->expiredAt ? (int) $this->expiredAt : null;
    }

    /**
     * Check current session is active
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getStatus() === self::STATUS_ACTIVE && $this->getExpiredAt() > time();
    }

    /**
     * Get token for selected user:
     *  - get publisher token for owner
     *  - generate token for other users or guests
     * @param User|null $user
     * @return string
     */
    public function getToken(?User $user = null): string
    {
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
     * @return string
     */
    public function createSubscriberToken(): string
    {
        $options = [
            'role' => Role::SUBSCRIBER,
            'expireTime' => $this->getExpiredAt()
        ];
        return Yii::$app->vonage->getToken($this->getSessionId(), $options);
    }

    /**
     * Create token for stream publisher
     * @return string
     */
    public function createPublisherToken(): string
    {
        $options = [
            'role' => Role::MODERATOR,
            'expireTime' => $this->getExpiredAt()
        ];
        return Yii::$app->vonage->getToken($this->getSessionId(), $options);
    }

    /**
     * Get current active session for selected shop(by id)
     *
     * @param int $shopId
     * @return self|null
     */
    public static function getCurrent($shopId): ?self
    {
        return self::find()->byShopId($shopId)->active()->orderByLatest()->one();
    }

    /**
     * Start translation for selected shop
     * Do not allow create new session if active exist
     * @param Shop $shop
     * @return self
     * @throws UnprocessableEntityHttpException
     */
    public static function startTranslation(Shop $shop): self
    {
        $session = self::getCurrent($shop->id);
        if ($session) {
            throw new UnprocessableEntityHttpException(ErrorList::errorTextByCode(ErrorList::STREAM_IN_PROGRESS));
        }
        return self::create($shop->id);
    }

    /**
     * Stop active translation for selected shop
     * We will assume that it is considered a success to bring the store to the "no active broadcasts" state.
     * If they are not already there - success
     * @param Shop $shop
     * @return self
     * @throws UnprocessableEntityHttpException
     */
    public static function stopTranslation(Shop $shop): bool
    {
        $session = self::getCurrent($shop->id);
        if (!$session) {
            return true;
        }
        return $session->stop();
    }

    /**
     * Stop session
     * @todo: add stop logic
     */
    public function stop()
    {
        $this->status = self::STATUS_STOPPED;
        return $this->save(true, ['status']);
    }

    /**
     * Create session for specified user.
     *
     * At the moment, a user can only belong to one shop.
     * Accordingly, we can say that if a user tries to start a stream, he starts only within his store.
     * If there is no store, the user will not be able to start the stream
     *
     * OpenTok sessions do not expire. However, authentication tokens do expire (see the generateToken() method).
     * Also note that sessions cannot explicitly be destroyed.
     *
     * @param int $shopId
     * @return self (return created model or model with errors)
     */
    public static function create(int $shopId): self
    {
        //set shop id and expired date (of publisher token)
        $session = new self([
            'shopId' => $shopId,
            'expiredAt' => time() + self::DEFAULT_DURATION,
        ]);

        //1. Validate shop for broadcast start (check is active)
        if (!$session->validate(['shopId'])) {
            return $session; //return model errors
        }

        // 2. Create session with tokbox and get token
        try {
            $session->sessionId = Yii::$app->vonage->createSession();
        } catch (Throwable $ex) {
            $session->addError('sessionId', $ex->getMessage());
            return $session; //return model with errors
        }

        // 3. Create token
        try {
            $session->publisherToken = $session->createPublisherToken();
        } catch (Throwable $ex) {
            $session->addError('publisherToken', $ex->getMessage());
            return $session; //return model with errors
        }

        // 4. Store session into DB
        $session->save();
        return $session; //return model or model with erros
    }
}
