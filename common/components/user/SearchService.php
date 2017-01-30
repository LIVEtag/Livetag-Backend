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
     * @var string
     */
    private $searchString;

    /**
     * SearchService constructor.
     * @param string $usernameOrEmail
     */
    public function __construct($usernameOrEmail)
    {
        $this->searchString = $usernameOrEmail;
    }

    /**
     * @return null|User
     */
    public function getUser()
    {
        $user = User::findByUsername($this->searchString);
        if ($user === null) {
            $user = User::findByEmail($this->searchString);
        }

        return $user;
    }
}
