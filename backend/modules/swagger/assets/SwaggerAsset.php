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
    public $css = [];

    /**
     * @inheritdoc
     */
    public $js = [
        'lib/jquery-migrate-1.4.1.js',
        'lib/jquery.slideto.min.js',
        'lib/jquery.wiggle.min.js',
        'lib/jquery.ba-bbq.js',
        'lib/handlebars-2.0.0.js',
        'lib/underscore-min.js',
        'lib/backbone-min.js',
        'lib/highlight.7.3.pack.js',
        'lib/jsoneditor.min.js',
        'lib/marked.js',
        'lib/swagger-oauth.js',
        'swagger-ui.js',
        'app.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        \yii\web\YiiAsset::class,
    ];
}
