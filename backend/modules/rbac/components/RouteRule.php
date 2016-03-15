<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\components;

use yii\rbac\Rule;

/**
 * Class RouteRule
 *
 * RouteRule Rule for check route with extra params
 */
class RouteRule extends Rule
{
    const RULE_NAME = 'route_rule';

    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        $routeParams = isset($item->data['params']) ? $item->data['params'] : [];
        $allow = true;
        $queryParams = \Yii::$app->request->getQueryParams();
        foreach ($routeParams as $key => $value) {
            $allow = $allow && (!isset($queryParams[$key]) || $queryParams[$key] == $value);
        }

        return $allow;
    }
}
