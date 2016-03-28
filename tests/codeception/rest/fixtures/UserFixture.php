<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace tests\codeception\rest\fixtures;

use rest\common\models\User;
use yii\test\ActiveFixture;

/**
 * Class UserFixture
 */
class UserFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $modelClass = User::class;

    /**
     * @inheritdoc
     */
    public function unload()
    {
        parent::unload();
        $this->resetTable();
    }
}
