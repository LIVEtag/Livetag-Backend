<?php
/**
 * UrlManager component configurations (for "backend" application)
 */
return [
    'class' => yii\web\UrlManager::class,
    'baseUrl' => '/backend',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        '' => '',
        '<controller:[\w\-]+>/<action:[\w\-]+>' => '<controller>/<action>/',
        '<module:\w+>/<controller:[\w\-]+>' => '<module>/<controller>/',
        '<module:\w+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/<controller>/<action>/',
    ],
];
