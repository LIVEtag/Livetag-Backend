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
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * This is the model class for table "shop".
 *
 * @property integer $id
 * @property string $name
 * @property string $uri
 * @property string $website
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property-read StreamSession[] $streamSessions
 * @property-read User[] $users
 *
 * EVENTS:
 * - EVENT_BEFORE_DELETE
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
     * @inherritdoc
     */
    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_DELETE
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'uri',
                'ensureUnique' => true,
                'immutable' => true
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'website'], 'required'],
            [['name', 'uri'], 'string', 'max' => 50],
            [
                'uri',
                'filter',
                'filter' => function ($value) {
                    return Inflector::slug($value, '-', true);
                },
            ],
            ['uri', 'unique'],
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
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'uri' => Yii::t('app', 'Livestream URI'),
            'website' => Yii::t('app', 'Website'),
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
            'uri',
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
