<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\Stream;

use common\components\validation\validators\ArrayFilter;
use common\models\Stream\StreamSession;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StreamSessionSearch extends StreamSession
{
    /**
     * Status Names
     */
    const STATUSES = [
        self::STATUS_NEW => 'New',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_STOPPED => 'Stopped',
        self::STATUS_ARCHIVED => 'Archived',
    ];

    public $announcedAtFrom;
    public $announcedAtTo;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['announcedAtFrom', 'announcedAtTo'], 'integer'],
            ['status', ArrayFilter::class],
            ['status', 'each', 'rule' => ['integer']],
            ['status', 'each', 'rule' => ['in', 'range' => array_keys(self::STATUSES)]],
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
     * @param int $shopId
     * @return ActiveDataProvider|self
     */
    public function search($params, int $shopId)
    {
        $this->setAttributes($params);
        if (!$this->validate()) {
            return $this;
        }

        $query = StreamSession::find()->published()->byShopId($shopId);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['createdAt' => SORT_DESC],
            ],
        ]);

        $query
            ->andFilterWhere([self::tableName() . '.status' => $this->status])
            ->andFilterWhere(['<=', self::tableName() . '.announcedAt', $this->announcedAtTo])
            ->andFilterWhere(['>=', self::tableName() . '.announcedAt', $this->announcedAtFrom]);

        return $dataProvider;
    }
}