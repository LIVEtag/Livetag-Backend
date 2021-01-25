<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Stream;

use backend\models\Stream\StreamSession;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * StreamSessionSearch represents the model behind the search form of `backend\models\Stream\StreamSession`.
 */
class StreamSessionSearch extends StreamSession
{

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id', 'shopId', 'status'], 'integer'],
            [['sessionId'], 'safe'],
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
        $query = StreamSession::find();

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
        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.shopId' => $this->shopId,
            self::tableName() . '.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', self::tableName() . '.sessionId', $this->sessionId]);

        return $dataProvider;
    }
}
