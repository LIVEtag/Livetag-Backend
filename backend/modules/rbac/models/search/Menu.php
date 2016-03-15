<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rbac\models\Menu as MenuModel;
use yii\db\ActiveQuery;

/**
 * Class Menu
 *
 * Menu represents the model behind the search form
 */
class Menu extends MenuModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent', 'order'], 'integer'],
            [['name', 'route', 'parentName'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Searching menu
     *
     * @param  array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = MenuModel::find()
            ->from(MenuModel::tableName() . ' t')
            ->joinWith(
                [
                    'menuParent' => function (ActiveQuery $query) {
                        $query->from(MenuModel::tableName() . ' parent');
                    }
                ]
            );

        $dataProvider = new ActiveDataProvider(['query' => $query]);

        $sort = $dataProvider->getSort();
        $sort->attributes['menuParent.name'] = [
            'asc' => ['parent.name' => SORT_ASC],
            'desc' => ['parent.name' => SORT_DESC],
            'label' => 'parent',
        ];
        $sort->attributes['order'] = [
            'asc' => ['parent.order' => SORT_ASC, 't.order' => SORT_ASC],
            'desc' => ['parent.order' => SORT_DESC, 't.order' => SORT_DESC],
            'label' => 'order',
        ];
        $sort->defaultOrder = ['menuParent.name' => SORT_ASC];

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(
            [
                't.id' => $this->id,
                't.parent' => $this->parent,
            ]
        );

        $query->andFilterWhere(['like', 'lower(t.name)', strtolower($this->name)])
            ->andFilterWhere(['like', 't.route', $this->route])
            ->andFilterWhere(['like', 'lower(parent.name)', strtolower($this->parentName)]);

        return $dataProvider;
    }
}
