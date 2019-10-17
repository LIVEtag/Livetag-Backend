<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace rest\common\models\views\User;

use common\models\User;
use rest\components\validation\ErrorList;
use rest\components\validation\ErrorMessage;
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
            ['newPassword', 'compare', 'compareAttribute' => 'confirmPassword'],
            ['newPassword', 'validateSame'],
        ];
    }

    /**
     * Compare and validate two fields of the password
     * @param $attribute
     */
    public function validateSame($attribute)
    {
        if ($this->newPassword == $this->password) {
            $this->addError(
                $attribute,
                (new ErrorMessage(
                    'New password can not be the same as old password',
                    ErrorList::SAME_CURRENT_PASSWORD_AND_NEW_PASSWORD
                ))
            );
        }
    }

    /**
     * Change user password.
     * @param User $user
     * @return bool|User $user
     * @throws Exception
     */
    public function changePassword(User $user)
    {
        if (!$this->validate()) {
            return false;
        }
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError(
                'password',
                new ErrorMessage('Current password is wrong.', ErrorList::CURRENT_PASSWORD_IS_WRONG)
            );
            return false;
        }
        $user->setPassword($this->newPassword);
        if (!$user->save()) {
            $this->addError('newPassword', 'New password was not changed');
            return false;
        }
        return $user;
    }
}
