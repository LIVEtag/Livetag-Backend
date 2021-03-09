<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Shop;

use backend\models\Shop\Shop;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * ShopSearch represents the model behind the search form of `backend\models\Shop\Shop`.
 */
class ShopSearch extends Shop
{

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'website', 'uri'], 'safe'],
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
        $query = Shop::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => ArrayHelper::getValue($params, 'pageSize', 20)
            ],
            'sort' => [
                'defaultOrder' => ['createdAt' => SORT_DESC]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            //do not return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([self::tableName() . '.id' => $this->id]);

        $query->andFilterWhere(['like', self::tableName() . '.name', $this->name])
            ->andFilterWhere(['like', self::tableName() . '.uri', $this->uri])
            ->andFilterWhere(['like', self::tableName() . '.website', $this->website]);

        return $dataProvider;
    }
}
