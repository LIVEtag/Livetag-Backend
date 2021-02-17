<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace backend\models\Comment;

use common\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * CommentSearch represents the model behind the search form of `backend\models\Comment\Comment`.
 */
class CommentSearch extends Comment
{
    public $username;
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id', 'streamSessionId'], 'integer'],
            [['username'], 'string'],
            [['message', 'username'], 'safe'],
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
        $query = Comment::find()->joinWith([self::REL_USER]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => ArrayHelper::getValue($params, 'pageSize', 20)
            ],
            'sort' => [
                'defaultOrder' => ['createdAt' => SORT_ASC]
            ],
        ]);
    
        $dataProvider->sort->attributes['username'] = [
            'asc' => [User::tableName() . '.name' => SORT_ASC],
            'desc' => [User::tableName() . '.name' => SORT_DESC],
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
        ]);
        $query->andFilterWhere(['like', self::tableName() . '.message', $this->message]);
        $query->andFilterWhere([
            'OR',
            [
                'like' , User::tableName() . '.name' ,
                $this->username
            ],
            [
                'like' , User::tableName() . '.email' ,
                $this->username
            ],
        ]);
        return $dataProvider;
    }
}
