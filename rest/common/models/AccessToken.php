<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace rest\common\models;

use rest\common\models\queries\User\AccessTokenQuery;
use rest\common\models\queries\User\UserQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class AccessToken
 *
 * @property integer $id
 * @property integer $userId
 * @property string $token
 * @property string $userIp
 * @property string $userAgent
 * @property integer $createdAt
 * @property integer $expiredAt
 */
class AccessToken extends ActiveRecord
{
    /**
     * One week
     */
    const REMEMBER_ME_TIME = 604800;

    /**
     * One hour
     */
    const NOT_REMEMBER_ME_TIME = 3600;

    const TOKEN_LENGTH = 128;

    const RANDOM_HASH_LENGTH = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%access_token}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => false
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'token'], 'required'],
            [['userId'], 'integer'],
            [['userAgent'], 'string'],
            [['userIp'], 'string', 'max' => 46],
            [['userIp', 'userAgent'], 'default', 'value' => ''],
            [['token'], 'string', 'min' => self::TOKEN_LENGTH, 'max' => self::TOKEN_LENGTH],
            [['createdAt', 'expiredAt'], 'safe'],
        ];
    }

    /**
     * @return UserQuery|null
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
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
     * @return AccessTokenQuery
     */
    public static function find()
    {
        return \Yii::createObject(AccessTokenQuery::class, [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'token',
            'expiredAt',
        ];
    }

    /**
     * Generate token
     *
     * @param int|null $expireTime
     */
    public function generateToken($expireTime = null)
    {
        if ($expireTime === null) {
            $expireTime = AccessToken::NOT_REMEMBER_ME_TIME;
        }

        $this->expiredAt = $expireTime + time();
        $this->token = $this->createToken();
    }

    /**
     * @return string
     */
    private function createToken()
    {
        $security = \Yii::$app->getSecurity();

        $hash = $security->hashData(
            $this->userId,
            $security->generateRandomString(self::RANDOM_HASH_LENGTH)
        );
        $hash .= '_';

        return $hash . $security->generateRandomString(self::TOKEN_LENGTH - strlen($hash));
    }
}
