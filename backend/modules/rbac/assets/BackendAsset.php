<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\rbac\assets;

use yii\web\AssetBundle;

/**
 * Class BackendAsset
 */
class BackendAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@rbac/assets';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/yii.admin.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
