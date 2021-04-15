<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\models\Stream\StreamSession;

return [
    'start-with-rotate' => [
        [
            'dataComment' => 'Check correct start with rotate',
            'request' => [
                'rotate' => StreamSession::ROTATE_90,
            ],
        ]
    ],
];
