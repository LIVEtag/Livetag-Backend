<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\components\rbac;

use yii\base\InvalidConfigException;
use yii\rbac\Item;
use yii\rbac\PhpManager as BasePhpManager;
use yii\rbac\Role;

/**
 * Class PhpManager
 *
 * Base usages
 * ```
 * 'access' => [
 *     'class' => AccessControl::class,
 *     'rules' => [
 *         [
 *             'allow' => true,
 *             'actions' => ['deposit', 'pay', 'wallet'],
 *             'roles' => ['cardholder'],
 *         ],
 *     ],
 * ],
 * ```
 *
 */
class PhpManager extends BasePhpManager
{
    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    protected function checkAccessRecursive($user, $itemName, $params, $assignments): bool
    {
        if (!isset($this->items[$itemName])) {
            return false;
        }

        /* @var $item Item */
        $item = $this->items[$itemName];
        \Yii::debug($item instanceof Role ? "Checking role: $itemName" : "Checking permission : $itemName", __METHOD__);

        if ($this->executeRule($user, $item, $params)) {
            foreach ($this->children as $parentName => $children) {
                if (isset($children[$itemName])
                    && !$this->checkAccessRecursive($user, $parentName, $params, $assignments)
                ) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
