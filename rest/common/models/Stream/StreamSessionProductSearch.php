<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\Stream;

use common\components\validation\validators\ArrayFilter;
use common\models\Product\StreamSessionProduct;
use common\models\Stream\StreamSession;
use rest\common\models\Product\Product;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * StreamSessionProductSearch represents the model behind the search form of `backend\models\Post\StreamSessionProduct`.
 */
class StreamSessionProductSearch extends StreamSessionProduct
{
    /**
     * @var StreamSession
     */
    protected $streamSession;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['status', 'integer'],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            ['productId', ArrayFilter::class],
            ['productId', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * @param StreamSession $streamSession
     * @param array $config
     */
    public function __construct(StreamSession $streamSession, $config = [])
    {
        $this->streamSession = $streamSession;
        parent::__construct($config);
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
     * @return ActiveDataProvider|self
     */
    public function search($params)
    {
        $this->setAttributes($params);
        if (!$this->validate()) {
            return $this; //return errors when validation fails
        }

        $query = $this->streamSession->getStreamSessionProducts();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['productId' => SORT_ASC],
            ],
        ]);

        //Sort by product title
        $dataProvider->sort->attributes['productTitle'] = [
            'asc' => [Product::tableName() . '.title' => SORT_ASC],
            'desc' => [Product::tableName() . '.title' => SORT_DESC],
        ];

        //join need only for sort by profile name or when expand required
        if (array_key_exists('productTitle', $dataProvider->sort->getAttributeOrders()) ||
            ArrayHelper::isIn(self::REL_PRODUCT, $this->getExpand($params))
        ) {
            $query->joinWith(self::REL_PRODUCT);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            self::tableName() . '.productId' => $this->productId,
            self::tableName() . '.status' => $this->status
        ]);

        return $dataProvider;
    }

    /**
     * @see yii\rest\Serializer getRequestedFields()
     * @param array $params
     * @return array
     */
    protected function getExpand(array $params): array
    {
        $expand = ArrayHelper::getValue($params, 'expand');
        return is_string($expand) ? preg_split('/\s*,\s*/', $expand, -1, PREG_SPLIT_NO_EMPTY) : [];
    }
}
