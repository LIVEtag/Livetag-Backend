<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Shop;

use common\components\behaviors\TimestampBehavior;
use common\components\EventDispatcher;
use common\models\queries\Shop\ShopQuery;
use common\models\Stream\StreamSession;
use common\models\User;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop".
 *
 * @property integer $id
 * @property string $name
 * @property string $website
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property-read StreamSession[] $streamSessions
 * @property-read User[] $users
 *
 * EVENTS:
 * - EVENT_AFTER_DELETE
 * @see EventDispatcher
 */
class Shop extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%shop}}';
    }

    /**
     * @inheritdoc
     * @return ShopQuery the active query used by this AR class.
     */
    public static function find(): ShopQuery
    {
        return new ShopQuery(get_called_class());
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
            [['name', 'website'], 'required'],
            ['name', 'string', 'max' => 50],
            ['website', 'string', 'max' => 255],
            ['website', 'url', 'defaultScheme' => 'https']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'website' => 'Website',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'name',
            'website'
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'userId'])->viaTable('user_shop', ['shopId' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStreamSessions(): ActiveQuery
    {
        return $this->hasMany(StreamSession::class, ['shopId' => 'id']);
    }
}
