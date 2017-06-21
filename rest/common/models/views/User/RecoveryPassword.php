<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\views\User;

use rest\common\models\User;
use rest\common\services\User\RateRequestService;
use yii\base\Model;

/**
 * Class RecoveryPassword
 */
class RecoveryPassword extends Model
{
    /**
     * @var string
     */
    public $resetToken;
    /**
     * @var string
     */
    public $password;
    /**
     * @var string
     */
    public $confirmPassword;
    /**
     * @var User
     */
    private $user;

    /**
     * RecoveryPassword constructor.
     * @param User $user
     * @param array $config
     */
    public function __construct(User $user, array $config = [])
    {
        parent::__construct($config);
        $this->user = $user;
    }

    public function rules()
    {
        return [
            [['resetToken', 'password', 'confirmPassword'], 'required'],
            ['password', 'compare', 'compareAttribute' => 'confirmPassword', 'message' => 'Passwords do not match.']
        ];
    }

    /**
     * Set new users's password
     * @return RecoveryPassword $this
     */
    public function recovery()
    {
        if ($this->user) {
            if ($this->validate()) {
                $this->user->setPassword($this->password);
            } else {
                $this->addError('resetToken', 'Token is invalid.');
            }
            $this->user->removePasswordResetToken();
            $this->user->save();
        } else {
            $this->addError('resetToken', 'Token is invalid.');
        }

        return $this;
    }
}
