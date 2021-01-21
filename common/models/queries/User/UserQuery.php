<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\models\queries\User;

use common\models\User;
use common\components\db\ActiveQuery;

/**
 * Class UserQuery
 */
class UserQuery extends ActiveQuery
{

    /**
     * @param string $identity
     * @return $this
     */
    public function byEmail(string $identity): self
    {
        return $this->andWhere([$this->getFieldName('email') => $identity]);
    }

    /**
     * @param string $identity
     * @return $this
     */
    public function byUuid(string $identity): self
    {
        return $this->andWhere([$this->getFieldName('uuid') => $identity]);
    }

    /**
     * @return $this
     */
    public function active()
    {
        return $this->byStatus(User::STATUS_ACTIVE);
    }

    /**
     * @param string $status
     * @return $this
     */
    public function byStatus(string $status): self
    {
        return $this->andWhere([$this->getFieldName('status') => $status]);
    }

    /**
     * @param string $role
     * @return $this
     */
    public function byRole(string $role): self
    {
        return $this->andWhere([$this->getFieldName('role') => $role]);
    }
}
