<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\views\User;

use common\models\User;
use rest\components\validation\ErrorList;
use rest\components\validation\ErrorListInterface;
use yii\base\Model;

class SendRecoveryEmailForm extends Model
{
    /** @var string */
    public $email;

    /** @var User */
    private $user;

    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'string'],
            ['email', 'email'],
            ['email', 'validateEmail'],
        ];
    }

    public function validateEmail($attr)
    {
        $this->user = User::findByEmail($this->email);
        if (!$this->user) {
            $errorList = \Yii::createObject(ErrorListInterface::class);
            $this->addError(
                $attr,
                $errorList->createErrorMessage(ErrorList::USER_NOT_FOUND)->setParams(['email' => $this->email])
            );
        }
    }

    public function generateAndSendEmail()
    {
        $user = $this->user;
        $user->generatePasswordResetToken();
        if ($user->save()) {
            \Yii::$app
                ->mailer
                ->compose('recovery-password', ['user' => $user])
                ->setFrom(\Yii::$app->params['adminEmail'])
                ->setTo($user->email)
                ->send();
        }

        return $user->passwordResetToken;
    }
}
