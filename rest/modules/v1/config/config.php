<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
$versionFilePath = \Yii::getAlias('@rest/../') . 'version.txt';

return [
    'version' => [
        'major' => 0,
        'minor' => 1,
        'patch' => 1,
        'commit' => (file_exists($versionFilePath)) ? file_get_contents($versionFilePath) : null
    ],
    'parameters' => [
        'vonage' => [
            'apiKey' => getenv('VONAGE_API_KEY'),
        ],
        'centrifugo' => [
            'ws' => getenv('CENTRIFUGO_WEB_SOCKET'),
        ],
    ],
    'errors' => \Yii::createObject(\common\components\validation\ErrorList::class),
];
