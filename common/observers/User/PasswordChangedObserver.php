<?php
/**
 * Copyright © 2020 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\observers\User;

use backend\models\User\User as BackendUser;
use common\models\Session;
use common\models\User;
use rest\common\models\AccessToken;
use rest\common\models\User as RestUser;
use RuntimeException;
use Yii;
use yii\base\Event;

class PasswordChangedObserver
{
    /**
     * Invalidate sessions and tokens except current user (api or backend)
     * @param Event $event
     * @throws RuntimeException
     */
    public function execute(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;

        $sessionId = null;
        $accessTokenId = null;
        if ($user instanceof BackendUser) {
            $sessionId = Yii::$app->session->getId();
        } elseif ($user instanceof RestUser) {
            $accessTokenId = $user->getAccessToken() ? $user->getAccessToken()->id : null;
        }

        // Invalidate access tokens
        $accessTokensQuery = AccessToken::find()->byUserId($user->id);
        if ($accessTokenId) {
            $accessTokensQuery->andWhere(['<>', 'id', $accessTokenId]); //exclude current token
        }
        foreach ($accessTokensQuery->each() as $accessToken) {
            $accessToken->invalidate();
        }

        // Clear authKey
        $user->generateAuthKey();
        $user->save(true, ['authKey']);

        // Invalidate sessions
        $sessionsQuery = Session::find()->where(['userId' => $user->id]);
        if ($sessionId) {
            $sessionsQuery->andWhere(['<>', 'id', $sessionId]); //exclude current session
        }
        foreach ($sessionsQuery->each() as $session) {
            $session->delete();
        }
    }
}
