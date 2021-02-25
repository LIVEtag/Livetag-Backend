<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace rest\common\models;

use common\helpers\LogHelper;
use common\models\User as CommonUser;
use Yii;
use yii\db\IntegrityException;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\Json;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property AccessToken $accessToken
 */
class User extends CommonUser
{
    /** @var AccessToken */
    protected $accessToken;

    /**
     * @return AccessToken
     */
    public function getAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    /**
     * @param $token
     * @param null $type
     * @return User|void|IdentityInterface|null
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        switch ($type) {
            case HttpBasicAuth::class:
                return self::getOrCreateBuyer($token);
            case HttpBearerAuth::class:
                /** @var $accessToken AccessToken */
                $accessToken = AccessToken::find()
                    ->byToken($token)
                    ->valid()
                    ->one();

                if ($accessToken !== null) {
                    $user = static::findOne(['id' => $accessToken->userId, 'status' => self::STATUS_ACTIVE]);
                    if (!empty($user)) {
                        $user->accessToken = $accessToken; // set current access token
                    }
                    return $user;
                }
        }
        return null;
    }

    /**
     * Get existing Buyer record or create new one
     * @param string $uuid
     * @return self|null
     */
    public static function getOrCreateBuyer($uuid): ?self
    {
        return self::getBuyerByUuid($uuid)?:self::createBuyerByUuid($uuid);
    }

    /**
     * @param string $uuid
     * @return \self|null
     */
    public static function getBuyerByUuid($uuid): ?self
    {
        return self::find()->byRole(self::ROLE_BUYER)->byUuid($uuid)->one();
    }

    /**
     * @param string $uuid
     * @return \self|null
     */
    public static function createBuyerByUuid($uuid): ?self
    {
        $user = new self([
            'uuid' => $uuid,
            'role' => self::ROLE_BUYER
        ]);
        try {
            if (!$user->save()) {
                LogHelper::error('Failed to save new buyer', 'user', LogHelper::extraForModelError($user));
                return null;
            }
            return $user;
        } catch (IntegrityException $ex) {
            // Simulated situation - save two users at the same time, each has the same uuid added.
            // Both users, when saved, will check the existence of the uuid in the database.
            // Both will get negative and try to save.
            // That user, which is saved a little earlier, everything will be successful, but the second one
            // will receive an exception (SQLSTATE [23000]: Integrity constraint violation: 1062 Duplicate entry)
            Yii::warning(
                [
                    'msg' => 'Duplicate Buyer uuid creation',
                    'extra' => [
                        'error' => $ex->getMessage(),
                        'model' => Json::encode($user->toArray(), JSON_PRETTY_PRINT),
                        'trace' => $ex->getTraceAsString(),
                    ]
                ],
                'user'
            );
            return self::getBuyerByUuid($uuid);//one more chance
        }
    }
}
