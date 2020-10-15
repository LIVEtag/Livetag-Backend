<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace console\controllers;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Class SentryController
 */
class SentryController extends Controller
{
    /**
     * Send test error
     *
     * @return int
     */
    public function actionTest()
    {
        $this->stdout("Error was generated. Please check it in the sentry dashboard\n", Console::BG_GREEN);
        \Yii::error('Sentry Test Error');
        return ExitCode::OK;
    }
}
