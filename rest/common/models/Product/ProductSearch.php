<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\Product;

use common\models\Shop\Shop;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product
{
    public $slug;
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['status'], 'integer'],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            [['title', 'sku'], 'string', 'max' => 255],
            [['slug'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::class, 'targetAttribute' => ['slug' => 'uri']],
            [['slug'], 'string', 'max' => 50],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    
    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider|self
     */
    public function search($params)
    {
        $query = Product::find()->byStatus([Product::STATUS_DISPLAYED, Product::STATUS_PRESENTED])->joinWith(['shop']);
    
        $this->setAttributes($params);
    
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        if (!$this->validate()) {
            return $this;
        }
        
        $query->andFilterWhere(
            [
               'shop.uri' => $this->slug,
            ]
        );
        
        $query->andFilterWhere([
            self::tableName() . '.status' => $this->status,
        ]);

        $query
            ->andFilterWhere(['like', 'sku', $this->sku])
            ->andFilterWhere(['like', 'title', $this->title]);
        return $dataProvider;
    }
}
