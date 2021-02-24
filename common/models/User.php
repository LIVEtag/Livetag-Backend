<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models;

use common\components\behaviors\TimestampBehavior;
use common\models\queries\Shop\ShopQuery;
use common\models\queries\User\UserQuery;
use common\models\Shop\Shop;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $role
 * @property string $passwordHash
 * @property string $passwordResetToken
 * @property string $email
 * @property string $uuid
 * @property string $name
 * @property string $authKey
 * @property integer $status
 * @property integer $createdAt
 * @property integer $updatedAt
 * @property string $password write-only password
 * @property-read boolean $isAdmin
 * @property-read boolean $isSeller
 * @property-read boolean $isBuyer
 *
 * @property-read Shop $shop
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * When user restore his own password
     */
    const EVENT_PASSWORD_RESTORED = 'passwordRestored';

    /**
     * Note: for now statuses not used. No fake user delete
     * Disabled user (marked as deleted)
     * @todo change to blocked
     */
    const STATUS_DELETED = 0;

    /**
     * Default active user
     */
    const STATUS_ACTIVE = 10;

    /**
     * Admin of the widget integrated to the website, admin of the livestream.
     */
    const ROLE_SELLER = 'seller';

    /**
     * Admin of the SaaS platform.
     */
    const ROLE_ADMIN = 'admin';

    /**
     * The viewer of the livestream. Pseudo-user of the widget.
     */
    const ROLE_BUYER = 'buyer';

    /**
     * Role Names
     */
    const ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_SELLER => 'Seller',
        self::ROLE_BUYER => 'Buyer',
    ];

    /**
     * Status Names
     */
    const STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_DELETED => 'Blocked',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%user}}';
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
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name'], 'string', 'max' => 40],
            ['role', 'default', 'value' => self::ROLE_SELLER],
            ['role', 'in', 'range' => array_keys(self::ROLES)],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            [
                'email',
                'required',
                'when' => function ($model) {
                    return !$model->getIsBuyer();
                }
            ],
            [
                'uuid',
                'required',
                'when' => function ($model) {
                    return $model->getIsBuyer();
                }
            ],
            ['uuid', 'match', 'pattern' => '/^[0-9A-F]{8}-[0-9A-F]{4}-[1345][0-9A-F]{3}-[0-9A-F]{4}-[0-9A-F]{12}$/i'],
            ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'role',
            'name' => function () {
                return $this->getName();
            },
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return [
            'shop'
        ];
    }

    /**
     * Return na,e of current user
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Check current user is Admin
     * @return bool
     */
    public function getIsAdmin(): bool
    {
        return $this->role == self::ROLE_ADMIN;
    }

    /**
     * Check current user is Seller
     * @return bool
     */
    public function getIsSeller(): bool
    {
        return $this->role == self::ROLE_SELLER;
    }

    /**
     * Check current user is Buyer
     * @return bool
     */
    public function getIsBuyer(): bool
    {
        return $this->role == self::ROLE_BUYER;
    }

    /**
     * @return ShopQuery
     */
    public function getShop(): ShopQuery
    {
        return $this->hasOne(Shop::class, ['id' => 'shopId'])->viaTable('user_shop', ['userId' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne(
            [
                'passwordResetToken' => $token,
                'status' => self::STATUS_ACTIVE,
            ]
        );
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->passwordHash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->passwordResetToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->passwordResetToken = null;
    }
}
