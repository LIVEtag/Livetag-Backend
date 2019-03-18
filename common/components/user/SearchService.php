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
     * @param $email
     * @return null|User
     */
    public function getUser($email)
    {
        $user = User::findByEmail($email);

        return $user;
    }
}
