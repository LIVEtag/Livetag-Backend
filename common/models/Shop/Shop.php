<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\Shop;

use common\components\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use common\models\queries\Shop\ShopQuery;

/**
 * This is the model class for table "shop".
 *
 * @property integer $id
 * @property string $name
 * @property string $website
 * @property integer $status
 * @property integer $createdAt
 * @property integer $updatedAt
 *
 * @property-read UserShop[] $userShops
 */
class Shop extends ActiveRecord
{
    /**
     * Disabled shop (marked as deleted)
     */
    const STATUS_DELETED = 0;

    /**
     * Default active shop
     */
    const STATUS_ACTIVE = 10;

    /**
     * Status Names
     */
    const STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_DELETED => 'Deleted',
    ];

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
            [['name', 'website'], 'string', 'max' => 255],
            ['website', 'url', 'defaultScheme' => 'https'],
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
            'id' => 'ID',
            'name' => 'Name',
            'website' => 'Website',
            'status' => 'Status',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
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
     * Fake delete
     * @todo: add delete logic
     */
    public function delete()
    {
        $this->status = self::STATUS_DELETED;
        return $this->save(true, ['status']);
    }
}
