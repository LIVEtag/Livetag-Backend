<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\models\views\User;

use common\models\User\SocialProfile;
use rest\common\models\AccessToken;
use rest\common\models\User;
use Yii;
use yii\base\Model;

/**
 * Class SocialForm
 */
class SocialForm extends Model
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $username;

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
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            [['userIp'], 'string', 'max' => 46],
            [['userAgent', 'socialId',], 'string'],
            [['socialType'], 'integer'],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'string', 'max' => 255],
            ['email', 'signupUser'],
        ];
    }

    /**
     * Sing up user
     */
    public function signupUser()
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
        $signupUser->username = $this->username;
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

        $transaction = Yii::$app->db->beginTransaction();
        try {

            $socialProfile = SocialProfile::findBySocialId($this->socialId);
            if ($socialProfile === null) {
                $socialProfile = new SocialProfile();
                $socialProfile->user_id = $this->user->id;
                $socialProfile->type = $this->socialType;
                $socialProfile->social_id = $this->socialId;
                $socialProfile->email = $this->email;

                if (!$socialProfile->save()) {
                    throw new \RuntimeException('Cannot create social profile');
                }
            }

            if (!$this->createAcessToken($this->user)) {
                throw new \RuntimeException('Cannot create aceess token');
            }

            $transaction->commit();

        } catch (\Exception $exception) {
            $transaction->rollBack();
            $this->addError('user', $exception->getMessage());
        }

        return $this->user;
    }

    /**
     * @param User $user
     * @return bool
     */
    private function createAcessToken(User $user)
    {
        $accessToken = AccessToken::find()->findCurrentToken(
            $this->userAgent,
            $this->userIp
        )->andWhere(
            'user_id = :user_id',
            [':user_id' => $user->id]
        )->one();

        if ($accessToken !== null) {
            return true;
        }

        $accessToken = new AccessToken();
        $accessToken->user_id = $user->id;
        $accessToken->generateToken(AccessToken::NOT_REMEMBER_ME_TIME);
        $accessToken->user_ip = $this->userIp;
        $accessToken->user_agent = $this->userAgent;

        return $accessToken->save();
    }
}
