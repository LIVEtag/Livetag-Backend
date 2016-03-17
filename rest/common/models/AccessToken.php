<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models;

use rest\common\models\queries\User\UserQuery;
use Yii;
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
 * @property boolean $is_verify_ip
 * @property boolean $is_frozen_expire
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
            [['token'], 'string', 'min' => self::TOKEN_LENGTH, 'max' => self::TOKEN_LENGTH],
            [['is_frozen_expire', 'is_verify_ip'], 'boolean'],
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
     */
    public function generateToken()
    {
        $this->token = $this->createToken();
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    private function createToken()
    {
        $security = Yii::$app->getSecurity();

        $hash = $security->hashData(
            $this->user_id,
            $security->generateRandomString(self::RANDOM_HASH_LENGTH)
        );
        $hash .= '_';

        return $hash . $security->generateRandomString(AccessToken::TOKEN_LENGTH - strlen($hash));
    }
}
