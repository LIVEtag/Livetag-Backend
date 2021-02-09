<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Product;

use backend\models\Product\StreamSessionProduct;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * StreamSessionProductSearch represents the model behind the search form of `backend\models\Product\StreamSessionProduct`.
 */
class StreamSessionProductSearch extends StreamSessionProduct
{
    public $sku;
    public $title;
    public $link;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id', 'streamSessionId', 'productId', 'status'], 'integer'],
            [['sku', 'title', 'link'], 'safe'],
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
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = StreamSessionProduct::find()
            ->joinWith(self::REL_PRODUCT);

        $dataProvider = new ActiveDataProvider([
            'pagination' => [
                'pageSize' => ArrayHelper::getValue($params, 'pageSize', 20)
            ],
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['sku'] = [
            'asc' => [Product::tableName() . '.sku' => SORT_ASC],
            'desc' => [Product::tableName() . '.sku' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['title'] = [
            'asc' => [Product::tableName() . '.title' => SORT_ASC],
            'desc' => [Product::tableName() . '.title' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['link'] = [
            'asc' => [Product::tableName() . '.link' => SORT_ASC],
            'desc' => [Product::tableName() . '.link' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            //do not return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.streamSessionId' => $this->streamSessionId,
            self::tableName() . '.productId' => $this->productId,
            self::tableName() . '.status' => $this->status,
        ]);

        //Product filter
        $query->andFilterWhere(['like', Product::tableName() . '.sku', $this->sku])
            ->andFilterWhere(['like', Product::tableName() . '.title', $this->title])
            ->andFilterWhere(['like', Product::tableName() . '.link', $this->link]);


        return $dataProvider;
    }
}
