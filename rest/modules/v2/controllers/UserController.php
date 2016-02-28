<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\modules\v2\controllers;

use rest\common\controllers\UserController as CommonUserController;
use rest\modules\v1\models\User\Signup;

/**
 * Class UserController
 */
class UserController extends CommonUserController
{
    public $modelClass = Signup::class;
}
