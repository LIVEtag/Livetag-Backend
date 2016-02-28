<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\modules\v2\controllers;

use rest\common\controllers\AccessTokenController as CommonAccessTokenController;
use rest\modules\v2\models\AccessToken\Create;

/**
 * Class AccessTokenController
 */
class AccessTokenController extends CommonAccessTokenController
{
    public $modelClass = Create::class;
}
