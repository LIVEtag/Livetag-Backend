<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\components\rbac\data;

use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;

/**
 * Class AccessService
 */
class AccessService extends Component
{
    /**
     * @var string
     */
    public $itemFile;

    /**
     * @var string[]
     */
    private $items = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->itemFile = \Yii::getAlias($this->itemFile);
        $this->items = $this->loadFromFile();
    }

    /**
     * Base usages
     * ```
     * $query = new DataQuery();
     * \Yii::$app->dataAccessManager->applyRules($query, ['userId' => \Yii::$app->user->id]);
     * $list = $query->all();
     * $list === count(Records to which access is allowed)
     * ```
     * Config in 'items.php' (example)
     * ```
     * // ......
     * \common\models\User\SocialProfile::class => \common\components\rbac\data\rules\UserOwnerRule::class
     *
     * // ......
     * ```
     * Init service (example - main.php)
     * ```
     * 'dataAccessManager' => [
     *     'class' => \common\components\rbac\data\AccessService::class,
     *     'itemFile' => '@common/components/rbac/data/items.php',
     * ],
     * ```
     *
     * @param ActiveQuery $query
     * @param array $params
     * @return void
     */
    public function applyRules(ActiveQuery $query, array $params = []): void
    {
        $class = $query->modelClass;
        if (isset($this->items[$class])) {
            /** @var QueryRuleInterface $ruleItem */
            $ruleItem = \Yii::createObject($this->items[$class]);
            if (!$ruleItem instanceof QueryRuleInterface) {
                throw new InvalidArgumentException(
                    \get_class($ruleItem) . ' must implement the QueryRuleInterface'
                );
            }
            $ruleItem->execute($query, $params);
        }
    }

    /**
     * @return array
     */
    protected function loadFromFile(): array
    {
        if (is_file($this->itemFile)) {
            return require $this->itemFile;
        }

        return [];
    }
}
