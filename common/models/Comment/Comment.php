<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Comment;

use common\components\behaviors\TimestampBehavior;
use common\models\queries\Comment\CommentQuery;
use common\models\Stream\StreamSession;
use common\models\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "comment".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $streamSessionId
 * @property string $message
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property-read StreamSession $streamSession
 * @property-read User $user
 */
class Comment extends ActiveRecord
{
    /**
     * Seller can answer with any size and any html
     */
    const SCENARIO_SELLER = 'seller';

    /**
     * User relation key
     */
    const REL_USER = 'user';

    /**
     * StreamSession relation key
     */
    const REL_STREAM_SESSION = 'streamSession';

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%comment}}';
    }

    /**
     * @inheritdoc
     * @return CommentQuery the active query used by this AR class.
     */
    public static function find(): CommentQuery
    {
        return new CommentQuery(get_called_class());
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
     *
     * Buyer can only post 255 length message without html. (default scanario)
     * Seller can answer with any size and any html
     *
     * @return array
     */
    public function scenarios()
    {
        $default = ArrayHelper::getValue(parent::scenarios(), self::SCENARIO_DEFAULT);
        return [
            self::SCENARIO_DEFAULT => $default,
            self::SCENARIO_SELLER => $default,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['userId', 'streamSessionId', 'message'], 'required'],
            [['userId', 'streamSessionId'], 'integer'],
            [['message'], 'string'],
            //Message for Buyer
            ['message', 'string', 'max' => 255, 'on' => [self::SCENARIO_DEFAULT]],
            ['message', PurifyFilter::class, 'on' => [self::SCENARIO_DEFAULT]],
            //Message for Seller
            ['message', 'string', 'on' => [self::SCENARIO_SELLER]],
            [['streamSessionId'], 'exist', 'skipOnError' => true, 'targetClass' => StreamSession::class, 'targetRelation' => 'streamSession'],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetRelation' => 'user'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'streamSessionId' => Yii::t('app', 'Stream Session ID'),
            'message' => Yii::t('app', 'Message'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'userId',
            'message',
            'createdAt'
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        return [
            self::REL_USER,
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getStreamSession(): ActiveQuery
    {
        return $this->hasOne(StreamSession::class, ['id' => 'streamSessionId']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }
}
