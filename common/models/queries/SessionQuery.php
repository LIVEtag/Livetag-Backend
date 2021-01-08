<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\models\queries;

use common\models\Session;
use common\components\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Session]].
 *
 * @see Session
 */
class SessionQuery extends ActiveQuery
{
    /**
     * @param int $userId
     * @return $this
     */
    public function byUserId(int $userId): ?self
    {
        return $this->andWhere(['userId' => $userId]);
    }
}
