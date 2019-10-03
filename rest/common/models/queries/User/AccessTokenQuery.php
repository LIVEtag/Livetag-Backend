<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

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

    /**
     * @param string $apiKey
     * @return $this
     */
    public function byToken(string $apiKey)
    {
        return $this->andWhere([AccessToken::tableName() . '.token' => $apiKey]);
    }

    /**
     * Filter by valid tokens
     * @return $this
     */
    public function valid()
    {
        return $this->andWhere(['>', AccessToken::tableName() . '.expiredAt', time()]);
    }
}
