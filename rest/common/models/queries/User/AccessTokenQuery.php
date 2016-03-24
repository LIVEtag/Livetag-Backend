<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\queries\User;

use rest\common\models\AccessToken;
use yii\db\ActiveQuery;

/**
 * Class AccessTokenQuery
 */
class AccessTokenQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return AccessToken|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
