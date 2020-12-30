<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\User;

use common\models\User as BaseModel;
use yii\helpers\ArrayHelper;

/**
 * Represents the backend version of `common\models\User`.
 */
class User extends BaseModel
{

    /**
     * @return string
     */
    public function getRoleName(): ?string
    {
        return ArrayHelper::getValue(self::ROLES, $this->role);
    }

    /**
     * @return string
     */
    public function getStatusName(): ?string
    {
        return ArrayHelper::getValue(self::STATUSES, $this->status);
    }
}
