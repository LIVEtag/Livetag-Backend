<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\interfaces;

use rest\common\controllers\actions\User\NewPasswordAction;
use rest\common\observers\UpdateObserver;

interface RecoveryPasswordInterface
{
    /**
     * @param UpdateObserver $updateObserver
     * @return NewPasswordAction
     */
    public function setUpdateObserver($updateObserver);
}