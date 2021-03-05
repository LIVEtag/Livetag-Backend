<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\User;

use backend\models\User\User;
use common\models\Session;
use rest\common\models\AccessToken;
use RuntimeException;
use yii\base\Event;

class PasswordRestoredObserver
{
    /**
     * @param Event $event
     * @throws RuntimeException
     */
    public function execute(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;

        // Invalidate access tokens
        $accessTokensQuery = AccessToken::find()->byUserId($user->id);
        foreach ($accessTokensQuery->each() as $accessToken) {
            $accessToken->invalidate();
        }

         // Clear authKey
        $user->generateAuthKey();
        $user->save(true, ['authKey']);

        // Invalidate sessions
        $sessionsQuery = Session::find()->where(['userId' => $user->id]);
        foreach ($sessionsQuery->each() as $session) {
            $session->delete();
        }
    }
}
