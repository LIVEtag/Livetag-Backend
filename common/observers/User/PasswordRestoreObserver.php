<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\User;

use backend\models\User\User;
use common\models\AccessToken;
use common\models\Session;
use RuntimeException;
use yii\base\Event;

class PasswordRestoreObserver
{
    /**
     * @param Event $event
     * @throws RuntimeException
     */
    public function execute(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        /** @var AccessToken $accessToken */
        $accessTokens = AccessToken::find()->where(['userId' => $user->id])->all();
        if (\is_array($accessTokens)) {
            foreach ($accessTokens as $accessToken) {
                $accessToken->invalidate();
            }
        }
        $user->generateAuthKey();
        $user->save();
        $sessionList = Session::find()->where(['userId' => $user->id])->all();
        foreach ($sessionList as $session) {
            $session->delete();
        }
    }
}
