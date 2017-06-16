<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\models\views\User;

use rest\common\models\User;
use yii\base\Model;
use yii\db\Exception;

/**
 * Class ChangePassword
 *
 * @property object $user
 * @property string $password
 * @property string $newPassword
 * @property string $confirmPassword
 */
class ChangePassword extends Model
{

    public $password;
    public $newPassword;
    public $confirmPassword;

    public function rules()
    {
        return [
            [['password', 'newPassword', 'confirmPassword'], 'required'],
            ['newPassword', 'validateNewPassword'],
            ['newPassword', 'validateSame'],
        ];
    }

    /**
     * Validate Users's new password
     * @param $attribute
     */
    public function validateNewPassword($attribute)
    {
        if ($this->newPassword != $this->confirmPassword) {
            $this->addError($attribute, 'Passwords do not match');
        }
    }

    /**
     * Compare and validate two fields of the password
     * @param $attribute
     */
    public function validateSame($attribute)
    {
        if ($this->newPassword == $this->password) {
            $this->addError($attribute, 'New password can not be the same as old password');
        }
    }

    /**
     * Change user password.
     * @param User $user
     * @throws Exception
     * @return bool|null
     */
    public function changePassword(User $user)
    {
        if (!$user || !$user->validatePassword($this->password, $user->password_hash)) {
            $this->addError('password', 'Wrong password');
            return false;
        }
        $user->setPassword($this->newPassword);
        if (!$user->save()) {
            $this->addError('newPassword', 'New password was not changed');
            return false;
        }
        return;
    }
}
