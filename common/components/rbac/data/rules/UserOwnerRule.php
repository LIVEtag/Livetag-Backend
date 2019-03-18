<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace common\components\rbac\data\rules;

use common\components\rbac\data\ParamsReader;
use common\components\rbac\data\QueryRuleInterface;
use yii\db\Query;

/**
 * Class UserOwnerRule
 */
class UserOwnerRule implements QueryRuleInterface
{
    /**
     * @inheritdoc
     */
    public function execute(Query $query, array $params): void
    {
        $id = ParamsReader::readUserId($params);

        $query->andWhere(['userId' => $id]);
    }
}
