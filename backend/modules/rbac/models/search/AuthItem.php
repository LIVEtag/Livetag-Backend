<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\models\search;

use common\modules\rbac\components\DbManager;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\rbac\Item;

/**
 * Class AuthItem
 *
 * AuthItemSearch represents the model behind the search form about AuthItem
 */
class AuthItem extends Model
{
    const TYPE_ROUTE = 101;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $rule;

    /**
     * @var string
     */
    public $data;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description',], 'safe'],
            [['type'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('rbac-backend', 'Name'),
            'item_name' => Yii::t('rbac-backend', 'Name'),
            'type' => Yii::t('rbac-backend', 'Type'),
            'description' => Yii::t('rbac-backend', 'Description'),
            'ruleName' => Yii::t('rbac-backend', 'Rule Name'),
            'data' => Yii::t('rbac-backend', 'Data'),
        ];
    }

    /**
     * Search authitem
     *
     * @param array $params
     * @return ActiveDataProvider|ArrayDataProvider
     */
    public function search($params)
    {
        /* @var DbManager $authManager */
        $authManager = $this->getAuthManager();
        if ($this->type == Item::TYPE_ROLE) {
            $items = $authManager->getRoles();
        } else {
            $items = [];
            if ($this->type == Item::TYPE_PERMISSION) {
                foreach ($authManager->getPermissions() as $name => $item) {
                    if ($name[0] !== '/') {
                        $items[$name] = $item;
                    }
                }
            } else {
                foreach ($authManager->getPermissions() as $name => $item) {
                    if ($name[0] === '/') {
                        $items[$name] = $item;
                    }
                }
            }
        }
        if ($this->load($params)
            && $this->validate()
            && (trim($this->name) !== '' || trim($this->description) !== '')
        ) {
            $search = strtolower(trim($this->name));
            $desc = strtolower(trim($this->description));
            $items = array_filter($items, function ($item) use ($search, $desc) {
                return (empty($search) || strpos(strtolower($item->name), $search) !== false)
                    && (empty($desc) || strpos(strtolower($item->description), $desc) !== false);
            });
        }

        return new ArrayDataProvider(['allModels' => $items]);
    }

    /**
     * @return DbManager
     * @throws InvalidConfigException
     */
    private function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component.');
        }

        return $authManager;
    }
}
