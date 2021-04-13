<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\Stream\Archive;

use common\models\Analytics\StreamSessionProductEvent;
use common\models\Stream\StreamSession;
use rest\common\models\Product\Product;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductSearch extends Product
{
    /**
     * @var StreamSession
     */
    protected $streamSession;

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
     * @return ActiveDataProvider
     */
    public function search()
    {
        $productsId = StreamSessionProductEvent::find()
            ->select(['`productId`'])
            ->byStreamSessionId($this->streamSession->id)
            ->byProductTypes()
            ->distinct()
            ->column();

        return new ActiveDataProvider([
            'query' => Product::find()->byId($productsId),
        ]);
    }
}
