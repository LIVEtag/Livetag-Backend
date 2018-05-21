<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\components\rbac\data;

use yii\db\Query;

/**
 * Interface QueryRuleInterface
 */
interface QueryRuleInterface
{
    /**
     * @param Query $query
     * @param array $params
     * @return void
     */
    public function execute(Query $query, array $params): void;
}
