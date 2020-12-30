<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\User;

use backend\models\Shop\Shop;
use backend\models\User\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * UserSearch represents the model behind the search form of `backend\models\User\User`.
 */
class UserSearch extends User
{
    /** @var string */
    public $shopName;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id', 'status'], 'integer'],
            [['role', 'email', 'shopName'], 'safe'],
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
        $query = User::find()
            ->andWhere(['role' => self::ROLE_SELLER])
            ->joinWith('shop');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => ArrayHelper::getValue($params, 'pageSize', 20)
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            //do not return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->sort->attributes['shopName'] = [
            'asc' => [Shop::tableName() . '.name' => SORT_ASC],
            'desc' => [Shop::tableName() . '.name' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.status' => $this->status,
            self::tableName() . '.role' => $this->role,
        ]);

        $query->andFilterWhere(['like', self::tableName() . '.email', $this->email]);
        $query->andFilterWhere(['like', Shop::tableName() . '.name', $this->shopName]);

        return $dataProvider;
    }
}
