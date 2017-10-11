<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\components\api;

use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl as BaseAccessControl;

class AccessControl extends BaseAccessControl
{

    /**
     * forbidden message for denyCallback
     * @var string
     */
    public $denyMessage;

    /**
     * override denyCallback if message passed
     */
    public function init()
    {
        parent::init();
        if ($this->denyMessage) {
            $this->denyCallback = function () {
                throw new ForbiddenHttpException($this->denyMessage);
            };
        }
    }
}
