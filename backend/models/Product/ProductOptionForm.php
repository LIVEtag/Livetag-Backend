<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace backend\models\Product;

use Yii;
use yii\base\Model;

class ProductOptionForm extends Model
{
    /** @var string */
    public $sku;

    /** @var string */
    public $price;

    /** @var string */
    public $option;

    public function rules()
    {
        return [
            [['sku', 'price'], 'required'],
            [['sku', 'price', 'option'], 'string', 'max' => 255],
            [['price'], 'number'],
            [
                ['option'],
                'filter',
                'filter' => function ($value) {
                    return $value == '' ? null : $value;
                }
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'sku' => Yii::t('app', 'SKU'),
            'price' => Yii::t('app', 'Price'),
            'option' => Yii::t('app', 'Option'),
        ];
    }
}
