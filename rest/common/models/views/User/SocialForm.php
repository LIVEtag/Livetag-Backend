<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models\views\User;

use common\models\User\SocialProfile;
use rest\common\models\AccessToken;
use common\models\User;
use yii\base\Model;

/**
 * Class SocialForm
 */
class SocialForm extends Model
{
    private const IP_LENGHT = 46;
    private const EMAIL_LENGHT = 255;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $socialId;

    /**
     * @var string
     */
    public $userIp;

    /**
     * @var integer
     */
    public $socialType;

    /**
     * @var string
     */
    public $userAgent;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['userIp'], 'string', 'max' => self::IP_LENGHT],
            [['userAgent', 'socialId',], 'string'],
            [['socialType'], 'integer'],

            ['email', 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => self::EMAIL_LENGHT],
            ['email', 'signupUser'],
        ];
    }

    /**
     * Sing up user
     */
    public function signupUser(): void
    {
        if ($this->hasErrors()) {
            return;
        }
        $this->user = User::findByEmail($this->email);

        if ($this->user !== null) {
            return;
        }

        $signupUser = new SignupUser();
        $signupUser->email = $this->email;
        $signupUser->password = \Yii::$app->getSecurity()->generateRandomString();
        $signupUser->userAgent = $this->userAgent;
        $signupUser->userIp = $this->userIp;

        $this->user = $signupUser->signup();

        if ($this->user === null) {
            $this->addError('user', 'Cannot create user.');
        }
    }

    /**
     * Login user
     *
     * @throws \RuntimeException
     * @return User|bool
     * @throws \yii\db\Exception
     */
    public function login()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $socialProfile = SocialProfile::findBySocialId($this->socialId);
            if ($socialProfile === null) {
                $socialProfile = new SocialProfile();
                $socialProfile->userId = $this->user->id;
                $socialProfile->type = $this->socialType;
                $socialProfile->socialId = $this->socialId;
                $socialProfile->email = $this->email;

                if (!$socialProfile->save()) {
                    throw new \RuntimeException('Cannot create social profile');
                }
            }

            $accessToken = $this->createAcessToken($this->user);
            if (!$accessToken) {
                throw new \RuntimeException('Cannot create aceess token');
            }

            $transaction->commit();
        } catch (\Exception $exception) {
            $transaction->rollBack();
            $this->addError('user', $exception->getMessage());
        }

        return $accessToken ?? null;
    }

    /**
     * @param User $user
     * @return bool|AccessToken
     */
    private function createAcessToken(User $user)
    {
        $accessToken = new AccessToken();
        $accessToken->userId = $user->id;
        $accessToken->generateToken(AccessToken::NOT_REMEMBER_ME_TIME);
        $accessToken->userIp = $this->userIp;
        $accessToken->userAgent = $this->userAgent;

        if ($accessToken->save()) {
            return $accessToken;
        }

        return false;
    }
}
