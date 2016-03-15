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
use backend\modules\rbac\models\BizRule as MBizRule;
use backend\modules\rbac\components\RouteRule;

/**
 * Class BizRule
 */
class BizRule extends Model
{
    /**
     * Name of the rule
     *
     * @var string
     */
    public $name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('rbac-backend', 'Name'),
        ];
    }

    /**
     * Search BizRule
     *
     * @param array $params
     * @return ActiveDataProvider|ArrayDataProvider
     */
    public function search($params)
    {
        $authManager = $this->getAuthManager();
        $models = [];
        $included = !($this->load($params) && $this->validate() && trim($this->name) !== '');
        foreach ($authManager->getRules() as $name => $item) {
            if ($name != RouteRule::RULE_NAME
                && ($included || stripos($item->name, $this->name) !== false)
            ) {
                $models[$name] = new MBizRule($item);
            }
        }

        return new ArrayDataProvider(['allModels' => $models]);
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
