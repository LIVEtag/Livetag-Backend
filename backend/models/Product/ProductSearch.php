<?php

declare(strict_types = 1);

namespace backend\models\Product;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductSearch represents the model behind the search form of `backend\models\Product\Product`.
 */
class ProductSearch extends Product
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'shopId', 'status'], 'integer'],
            [['externalId', 'title', 'description', 'link'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find()->active();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.status' => $this->status,
            self::tableName() . '.shopId' => $this->shopId,
        ]);

        $query->andFilterWhere(['like', self::tableName() . '.externalId', $this->externalId])
            ->andFilterWhere(['like', self::tableName() . '.title', $this->title])
            ->andFilterWhere(['like', self::tableName() . '.link', $this->link]);
        return $dataProvider;
    }
}
