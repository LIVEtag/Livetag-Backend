<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
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
     * @param $searchString
     * @return null|User
     */
    public function getUser($searchString)
    {
        $user = User::findByUsername($searchString);
        if ($user === null) {
            $user = User::findByEmail($searchString);
        }

        return $user;
    }
}
