<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\assets;

use yii\web\AssetBundle;

/**
 * Class AutocompleteAsset
 */
class AutocompleteAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@rbac/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'css/jquery-ui.css',
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/jquery-ui.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
