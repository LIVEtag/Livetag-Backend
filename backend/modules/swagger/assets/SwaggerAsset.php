<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
namespace backend\modules\swagger\assets;

use yii\web\AssetBundle;

/**
 * Class SwaggerAsset
 */
class SwaggerAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@swagger/assets/dist';

    /**
     * @inheritdoc
     */
    public $css = [
        'css/index.css',
        'css/standalone.css',
        'css/api-explorer.css',
        'css/screen.css',
        'css/main.css',
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'lib/jquery-1.8.0.min.js',
        'lib/jquery.slideto.min.js',
        'lib/jquery.wiggle.min.js',
        'lib/jquery.ba-bbq.min.js',
        'lib/handlebars-2.0.0.js',
        'lib/underscore-min.js',
        'lib/backbone-min.js',
        'swagger-ui.min.js',
        'lib/highlight.7.3.pack.js',
        'lib/jsoneditor.min.js',
        'lib/marked.js',
        'lib/swagger-oauth.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [];
}
