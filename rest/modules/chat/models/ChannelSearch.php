<?php
namespace rest\modules\chat\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use rest\modules\chat\models\Channel;
use rest\common\models\User;

/**
 * ChannelSearch represents the model behind the search form about `rest\modules\chat\models\Channel`.
 */
class ChannelSearch extends Channel
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['url', 'name', 'description'], 'safe'],
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
    public function search($params, User $user)
    {
        $query = Channel::find();

        // add conditions that should always apply here
        $query->avaliableForUser($user);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
