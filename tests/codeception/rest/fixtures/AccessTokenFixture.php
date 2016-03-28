<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\rest\fixtures;

use rest\common\models\AccessToken;
use yii\test\ActiveFixture;

/**
 * Class AccessTokenFixture
 */
class AccessTokenFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $modelClass = AccessToken::class;

    /**
     * @inheritdoc
     */
    public function unload()
    {
        parent::unload();
        $this->resetTable();
    }
}
