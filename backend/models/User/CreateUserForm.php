<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\User;

use backend\models\Shop\Shop;
use common\models\User;
use Exception;
use Throwable;
use Yii;
use yii\base\Model;

/**
 * Class CreateUserForm
 */
class CreateUserForm extends Model
{
    const STRING_MAX_LENGTH = 255;
    const PASSWORD_DEFAULT_LENGTH = 16;

    /**
     * Stored user id
     * @var integer
     */
    public $id;

    /** @var string */
    public $email;

    /** @var string */
    public $name;

    /** @var string */
    public $shopId;

    /** @var string */
    protected $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'shopId'], 'required'],
            ['email', 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
            ['email', 'email'],
            ['name', 'string', 'max' => User::NAME_MAX_LENGTH],
            ['email', 'string', 'max' => self::STRING_MAX_LENGTH],
            [
                'email',
                'unique',
                'targetClass' => User::class,
            ],
            ['shopId', 'string'],
            [['shopId'], 'exist', 'skipOnError' => true, 'targetClass' => Shop::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'shopId' => 'Shop',
            'name' => 'Seller Name',
        ];
    }

    /**
     * @return bool
     * @throws Throwable
     * @throws Exception
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new User();
        $user->email = $this->email;
        $user->name = $this->name;
        $user->setPassword($this->getPassword());
        $user->generateAuthKey();
        $user->role = User::ROLE_SELLER;


        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Create user entity
            if (!$user->save()) {
                $this->addErrors($user->getErrors());
                $transaction->rollBack();
                return false;
            }
            //store profile id to use in redirrects
            $this->id = $user->id;

            //Create relation to shop
            $shop = Shop::findOne($this->shopId);
            $user->link('shop', $shop);

            // Send Email to seller (also could throw exception)
            $this->sendEmail($user);

            $transaction->commit();
            return true;
        } catch (Throwable $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    /**
     * Generate and return a random password
      If the password has already been generated, return it
     * @return string
     */
    protected function getPassword(): string
    {
        if (!$this->password) {
            $this->password = Yii::$app->security->generateRandomString(self::PASSWORD_DEFAULT_LENGTH);
        }
        return $this->password;
    }

    /**
     * @param User $user
     */
    public function sendEmail(User $user)
    {
        \Yii::$app->mailer
            ->compose('seller-created', [
                'link' => Yii::$app->urlManagerBackend->createAbsoluteUrl(['site/login']),
                'password' => $this->getPassword()
            ])
            ->setFrom(\Yii::$app->params['supportEmail'])
            ->setTo($user->email)
            ->setSubject('Welcome to the LiveTag!')
            ->send();
    }
}
