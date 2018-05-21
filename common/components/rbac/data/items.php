<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\components\rbac\data\rules\UserOwnerRule;
use common\models\User\SocialProfile;

return [
    SocialProfile::class => UserOwnerRule::class
];
