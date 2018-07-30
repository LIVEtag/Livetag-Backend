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
 * @property integer $user_id
 * @property string $token
 * @property string $user_ip
 * @property string $user_agent
 * @property integer $created_at
 * @property integer $expired_at
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
                'createdAtAttribute' => 'created_at',
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
            [['user_id', 'token'], 'required'],
            [['user_id'], 'integer'],
            [['user_agent'], 'string'],
            [['user_ip'], 'string', 'max' => 46],
            [['user_ip', 'user_agent'], 'default', 'value' => ''],
            [['token'], 'string', 'min' => self::TOKEN_LENGTH, 'max' => self::TOKEN_LENGTH],
            [['created_at', 'expired_at'], 'safe'],
        ];
    }

    /**
     * @return UserQuery|null
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
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
            'expired_at',
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

        $this->expired_at = $expireTime + time();
        $this->token = $this->createToken();
    }

    /**
     * @return string
     */
    private function createToken()
    {
        $security = \Yii::$app->getSecurity();

        $hash = $security->hashData(
            $this->user_id,
            $security->generateRandomString(self::RANDOM_HASH_LENGTH)
        );
        $hash .= '_';

        return $hash . $security->generateRandomString(self::TOKEN_LENGTH - strlen($hash));
    }
}
