<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace common\components\user;

use rest\common\models\User;

/**
 * Class SearchService
 */
class SearchService
{
    /**
     * @param $username
     * @return null|User
     */
    public function getUser($username)
    {
        $user = User::findByUsername($username);
        if ($user === null) {
            $user = User::findByEmail($username);
        }

        return $user;
    }
}
