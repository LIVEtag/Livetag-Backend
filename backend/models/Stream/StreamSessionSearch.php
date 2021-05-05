<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Stream;

use backend\models\Stream\StreamSession;
use common\models\Analytics\StreamSessionStatistic;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * StreamSessionSearch represents the model behind the search form of `backend\models\Stream\StreamSession`.
 */
class StreamSessionSearch extends StreamSession
{
    /** @var int */
    public $totalViewCount;

    /** @var int */
    public $totalAddToCartCount;

    /**
     * “Add to cart” rate =
     * Number of clicks on the “Add to cart” button during the session/the number of customers of the livestream
     * @var float
     */
    public $totalAddToCartRate;

    /**
     * Duration in seconds
     * @var int
     */
    public $actualDuration;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id', 'shopId', 'status', 'totalViewCount', 'totalAddToCartCount', 'duration'], 'integer'],
            ['totalAddToCartRate', 'number'],
            [['sessionId', 'name'], 'string'],
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

        $actualDurationExpression = new Expression('
            CASE WHEN ' . self::tableName() . '.startedAt IS NOT NULL
                THEN COALESCE(' . self::tableName() . '.stoppedAt, NOW()) - ' . self::tableName() . '.startedAt
                ELSE NULL
            END AS actualDuration');

        $query = self::find()
            ->joinWith(self::REL_STREAM_SESSION_STATISTIC)
            ->select([self::tableName() . '.*',
                StreamSessionStatistic::tableName() . '.totalAddToCartCount',
                StreamSessionStatistic::tableName() . '.totalViewCount',
                StreamSessionStatistic::tableName() . '.totalAddToCartRate',
                $actualDurationExpression
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => ArrayHelper::getValue($params, 'pageSize', 20)
            ],
            'sort' => [
                'defaultOrder' => ['announcedAt' => SORT_DESC]
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            //do not return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->sort->attributes['totalAddToCartCount'] = [
            'asc' => [StreamSessionStatistic::tableName() . '.totalAddToCartCount' => SORT_ASC],
            'desc' => [StreamSessionStatistic::tableName() . '.totalAddToCartCount' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['totalViewCount'] = [
            'asc' => [StreamSessionStatistic::tableName() . '.totalViewCount' => SORT_ASC],
            'desc' => [StreamSessionStatistic::tableName() . '.totalViewCount' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['totalAddToCartRate'] = [
            'asc' => [StreamSessionStatistic::tableName() . '.totalAddToCartRate' => SORT_ASC],
            'desc' => [StreamSessionStatistic::tableName() . '.totalAddToCartRate' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['actualDuration'] = [
            'asc' => ['actualDuration' => SORT_ASC],
            'desc' => ['actualDuration' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            self::tableName() . '.shopId' => $this->shopId,
            self::tableName() . '.status' => $this->status,
            self::tableName() . '.duration' => $this->duration,
            StreamSessionStatistic::tableName() . '.totalViewCount' => $this->totalViewCount,
            StreamSessionStatistic::tableName() . '.totalAddToCartCount' => $this->totalAddToCartCount,
            StreamSessionStatistic::tableName() . '.totalAddToCartRate' => $this->totalAddToCartRate,
        ]);

        $query->andFilterWhere(['like', self::tableName() . '.sessionId', $this->sessionId]);
        $query->andFilterWhere(['like', self::tableName() . '.name', $this->name]);

        return $dataProvider;
    }
}
