<?php

use common\fixtures\ProductFixture;
use common\models\Product\Product;
use yii\helpers\Json;

return [
    ProductFixture::PRODUCT_HIDDEN_1 => [
        'id' => ProductFixture::PRODUCT_HIDDEN_1,
        'options' => Json::encode([
            'price'=> $this->generator->randomFloat(2, 1, 10000),
            'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0]
        ]),
        'status' => Product::STATUS_HIDDEN,
    ],
    ProductFixture::PRODUCT_HIDDEN_2 => [
        'id' => ProductFixture::PRODUCT_HIDDEN_2,
        'options' => Json::encode([
            'price'=> $this->generator->randomFloat(2, 1, 10000),
            'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0]
        ]),
        'status' => Product::STATUS_HIDDEN,
    ],
    ProductFixture::PRODUCT_HIDDEN_3 => [
        'id' => ProductFixture::PRODUCT_HIDDEN_3,
        'options' => Json::encode([
            'price'=> $this->generator->randomFloat(2, 1, 10000),
            'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0],
            'defaultField' => 'HIDDEN TEST test TEST test TEST test'
        ]),
        'status' => Product::STATUS_HIDDEN,
    ],
    ProductFixture::PRODUCT_HIDDEN_4 => [
        'id' => ProductFixture::PRODUCT_HIDDEN_4,
        'options' => Json::encode([
            'price'=> $this->generator->randomFloat(2, 1, 10000),
            'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0]
        ]),
        'status' => Product::STATUS_HIDDEN,
    ],
    ProductFixture::PRODUCT_PRESENTED_1 => [
        'id' => ProductFixture::PRODUCT_PRESENTED_1,
        'options' => Json::encode([
            'price'=> $this->generator->randomFloat(2, 1, 10000),
            'color'=> $this->generator->colorName,
            'defaultField' => 'PRESENTED test TEST test TEST test'
        ]),
        'status' => Product::STATUS_PRESENTED,
    ],
    ProductFixture::PRODUCT_PRESENTED_2 => [
        'id' => ProductFixture::PRODUCT_PRESENTED_2,
        'options' => Json::encode([
            'price'=> $this->generator->randomFloat(2, 1, 10000),
        ]),
        'status' => Product::STATUS_PRESENTED,
    ],
    ProductFixture::PRODUCT_PRESENTED_3 => [
        'id' => ProductFixture::PRODUCT_PRESENTED_3,
        'options' => Json::encode([
            'price'=> $this->generator->randomFloat(2, 1, 10000),
            'color'=> $this->generator->colorName,
        ]),
        'status' => Product::STATUS_PRESENTED,
    ],
    ProductFixture::PRODUCT_PRESENTED_4 => [
        'id' => ProductFixture::PRODUCT_PRESENTED_4,
        'options' => Json::encode([
            'price'=> $this->generator->randomFloat(2, 1, 10000),
        ]),
        'status' => Product::STATUS_PRESENTED,
    ],
    ProductFixture::PRODUCT_DISPLAYED_1 => [
        'id' => ProductFixture::PRODUCT_DISPLAYED_1,
        'options' => Json::encode([
            'price'=> $this->generator->randomFloat(2, 1, 10000),
            'color'=> $this->generator->colorName,
            'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0]
        ]),
        'status' => Product::STATUS_DISPLAYED,
    ],
    ProductFixture::PRODUCT_DISPLAYED_2 => [
        'id' => ProductFixture::PRODUCT_DISPLAYED_2,
        'options' => Json::encode([
            'price'=> $this->generator->randomFloat(2, 1, 10000),
            'color'=> $this->generator->colorName,
            'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0]
        ]),
        'status' => Product::STATUS_DISPLAYED,
    ],
    ProductFixture::PRODUCT_DISPLAYED_3 => [
        'id' => ProductFixture::PRODUCT_DISPLAYED_3,
        'options' => Json::encode([
            'price'=> $this->generator->randomFloat(2, 1, 10000),
            'color'=> $this->generator->colorName,
            'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0],
            'defaultField' => 'TEST test TEST test TEST test'
        ]),
        'status' => Product::STATUS_DISPLAYED,
    ],
    ProductFixture::PRODUCT_DISPLAYED_4 => [
        'id' => ProductFixture::PRODUCT_DISPLAYED_4,
        'options' => Json::encode([
            'price'=> $this->generator->randomFloat(2, 1, 10000),
            'color'=> $this->generator->colorName,
            'size'=> $this->generator->randomElements(['XL', 'XXL', 'L', 'S', 'M', 'XS'], 1)[0]
        ]),
        'status' => Product::STATUS_DISPLAYED,
    ],
];
