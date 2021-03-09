<?php

namespace backend\assets;

use yii\web\AssetBundle;
use backend\assets\AppAsset;

class HighlightAsset extends AssetBundle
{
    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.6.0/highlight.min.js'
    ];
    public $css = [
        'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.6.0/styles/default.min.css',
    ];
    public $depends = [
        AppAsset::class,
    ];
}
