<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
Yii::setAlias('@chat', dirname(__DIR__));

use rest\modules\chat\components\CentrifugoComponent;

return [
    'components' => [
        'centrifugo' => [
            'class' => CentrifugoComponent::class,
            'host' => 'http://centrifugo:8000', //docker example(docker-compose link name)
            'secret' => 'gbksoft',
        ],
    ],
];
