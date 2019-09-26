<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\User;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\User;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_social_profile".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $type
 * @property string $socialId
 * @property string $email
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property User $user
 */
class SocialProfile extends ActiveRecord
{
    public const TYPE_GOOGLE = 1;
    public const TYPE_LINKEDIN = 2;
    public const TYPE_FACEBOOK = 3;
    public const TYPE_TWITTER = 4;

    private const MAX_STRING_LENGHT = 255;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%user_social_profile}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['userId', 'type', 'socialId', 'email'], 'required'],
            [['userId', 'type', 'createdAt', 'updatedAt'], 'integer'],
            [['socialId', 'email'], 'string', 'max' => self::MAX_STRING_LENGHT],
            [
                ['userId'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['userId' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'type' => 'Type',
            'socialId' => 'Social ID',
            'email' => 'Email',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    /**
     * @param string $socialId
     * @return ActiveRecord|null
     */
    public static function findBySocialId($socialId): ?ActiveRecord
    {
        return static::find()
            ->andWhere('socialId = :socialId', ['socialId' => $socialId])
            ->one();
    }
}
