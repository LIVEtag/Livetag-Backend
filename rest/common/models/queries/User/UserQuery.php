<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\queries\User;

use rest\common\models\User;
use yii\db\ActiveQuery;

/**
 * Class UserQuery
 */
class UserQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return User|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
