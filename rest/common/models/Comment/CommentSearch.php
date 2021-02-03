<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\Comment;

use common\models\Comment\Comment;
use common\models\Stream\StreamSession;
use rest\components\helpers\ExpandHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * CommentSearch represents the model behind the search form of `backend\models\Post\StreamSessionProduct`.
 */
class CommentSearch extends Comment
{
    /**
     * @var StreamSession
     */
    protected $streamSession;

    /**
     * @var integer
     */
    public $lastId;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['userId', 'lastId'], 'integer'],
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

        $query = $this->streamSession->getComments()
            ->orderBy(['id' => SORT_DESC]);

        //join need only when expand required
        if (ArrayHelper::isIn(self::REL_USER, ExpandHelper::getExpand($params))) {
            $query->joinWith(self::REL_USER);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // grid filtering conditions
        $query->andFilterWhere([self::tableName() . '.userId' => $this->userId]);


        if ($this->lastId) {
            $query->andWhere(['<', $query->getFieldName('id'), $this->lastId]);
        }

        return $dataProvider;
    }
}
