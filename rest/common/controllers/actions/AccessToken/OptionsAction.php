<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace rest\common\controllers\actions\AccessToken;

use yii\rest\OptionsAction as BaseOptionsAction;

/**
 * Class OptionsAction
 */
class OptionsAction extends BaseOptionsAction
{
    /**
     * @inheritdoc
     */
    public $collectionOptions = ['POST'];

    /**
     * @inheritdoc
     */
    public $resourceOptions = [];
}
