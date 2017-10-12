<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\components\api;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl as BaseAccessControl;

class AccessControl extends BaseAccessControl
{
    /**
     * override default smessage in forbidden exception
     * @var string
     */
    public $forbiddenMessage;

    /**
     * @inheritdoc
     */
    protected function denyAccess($user)
    {
        if ($user !== false && $user->getIsGuest()) {
            $user->loginRequired();
        } else {
            $forbiddenMessage = $this->forbiddenMessage ?
                $this->forbiddenMessage :
                Yii::t('yii', 'You are not allowed to perform this action.');
            throw new ForbiddenHttpException($forbiddenMessage);
        }
    }
}
