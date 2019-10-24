<?php
/**
 * Copyright © 2019 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use yii\test\ActiveFixture;
use common\models\AccessToken;

/**
 * Class AccessTokenFixture
 */
class AccessTokenFixture extends ActiveFixture
{
    public const USER = 1;
    public const DELETED = 2;

    public $modelClass = AccessToken::class;
    public $depends = [UserFixture::class];
}
