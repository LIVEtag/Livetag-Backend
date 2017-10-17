<?php
/**
 * Copyright © 2017 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use rest\modules\chat\components\CentrifugoComponent;

return [
    'components' => [
        'centrifugo' => [
            'class' => CentrifugoComponent::class,
            'host' => '{{CENTRIFUGO_HOST}}',
            'secret' => '{{CENTRIFUGO_SECRET}}',
            'ws' => '{{CENTRIFUGO_WEB_SOCKET}}',
        ],
    ],
];
