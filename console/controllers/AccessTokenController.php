<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace console\controllers;

use common\models\AccessToken;
use yii\console\Controller;

/**
 * Class AccessTokenController
 */
class AccessTokenController extends Controller
{
    /**
     * Clear all expired data from current application
     */
    public function actionClearExpired()
    {
        //delete user tokens if expired
        AccessToken::deleteAll('expiredAt < :expiredAt', [
            ':expiredAt' => time(),
        ]);
    }
}
