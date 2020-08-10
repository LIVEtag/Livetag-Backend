<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace console\controllers;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\Html;

/**
 * Class SmtpController
 */
class SmtpController extends Controller
{
    /**
     * @var string
     */
    public $emailTo;


    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return [
            'emailTo',
        ];
    }

    public function optionAliases()
    {
        return ['e' => 'emailTo'];
    }

    /**
     * Send test email to the provided address
     *
     * @return int
     */
    public function actionTest()
    {
        //check options are passed
        if (empty($this->emailTo)) {
            $this->stdout("emailTo option is required\n", Console::BG_RED);
            return ExitCode::OK;
        }

        // prepare subject and body
        $subject = 'Welcome to ' . \Yii::$app->name;
        $htmlBody = "<div class='confirm-email'>
            <p>Dear user,</p>
            <p>To complete your registration flow, please follow the " . Html::a('link', '#') . ".</p>
            <p>If you did not request the above email, ignore this message.</p>
        </div>";

        // send email
        try {
            // explicitly disable sending email to file
            \Yii::$app->mailer->useFileTransport = false;

            \Yii::$app->mailer->compose()
                ->setFrom(\Yii::$app->params['supportEmail'])
                ->setTo($this->emailTo)
                ->setSubject($subject)
                ->setHtmlBody($htmlBody)
                ->send();

            $this->stdout("Email has been successfully sent\n", Console::BG_GREEN);
        } catch (\Exception $e) {
            $this->stdout("Error while sending email: {$e->getMessage()}.\n", Console::BG_RED);
        }

        return ExitCode::OK;
    }
}
