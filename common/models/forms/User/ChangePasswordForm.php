<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\models\forms\User;

use common\components\validation\ErrorList;
use common\components\validation\ErrorListInterface;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * Class ChangePassword (used for both api and backend)
 *
 * @property object $user
 * @property string $password
 * @property string $newPassword
 * @property string $confirmPassword
 */
class ChangePasswordForm extends Model
{
    public $password;
    public $newPassword;
    public $confirmPassword;

    /** @var ErrorListInterface  */
    private $errorList;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->errorList = Yii::createObject(ErrorListInterface::class);
    }

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
                $this->errorList->createErrorMessage(ErrorList::SAME_CURRENT_PASSWORD_AND_NEW_PASSWORD)
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
                $this->errorList->createErrorMessage(ErrorList::CURRENT_PASSWORD_IS_WRONG)
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
