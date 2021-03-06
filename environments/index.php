<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

/**
 * The manifest of files that are local to specific environment.
 * This file returns a list of environments that the application
 * may be installed under. The returned data must be in the following
 * format:
 *
 * ```php
 * return [
 *     'environment name' => [
 *         'path' => 'directory storing the local files',
 *         'skipFiles'  => [
 *             // list of files that should only copied once and skipped if they already exist
 *         ],
 *         'setWritable' => [
 *             // list of directories that should be set writable
 *         ],
 *         'setExecutable' => [
 *             // list of files that should be set executable
 *         ],
 *         'setCookieValidationKey' => [
 *             // list of config files that need to be inserted with automatically generated cookie validation keys
 *         ],
 *         'createSymlink' => [
 *             // list of symlinks to be created. Keys are symlinks, and values are the targets.
 *         ],
 *     ],
 * ];
 * ```
 */
return [
    'Dev' => [
        'path' => 'dev',
        'setWritable' => [
            'rest/runtime',
            'rest/web/assets',
            'backend/runtime',
            'backend/web/assets',
        ],
        'createSessionDirectory' => [
            'backend/runtime/session',
        ],
        'setExecutable' => [
            'yii',
            'yii_test',
            'tests/codeception/bin/yii',
        ],
        'setCookieValidationKey' => [
            'backend/config/main-local.php',
            'rest/config/main-local.php',
        ],
        'setRequestBaseUrl' => [
            'backend/config/main-local.php',
            'rest/config/main-local.php',
        ],
    ],
    'Test' => [
        'path' => 'test',
        'setWritable' => [
            'rest/runtime',
            'rest/web/assets',
            'backend/runtime',
            'backend/web/assets',
        ],
        'createSessionDirectory' => [
            'backend/runtime/session',
        ],
        'setExecutable' => [
            'yii',
            'tests/codeception/bin/yii',
            'yii_test',
        ],
        'setCookieValidationKey' => [
            'backend/config/main-local.php',
            'rest/config/main-local.php',
        ],
        'setRequestBaseUrl' => [
            'backend/config/main-local.php',
            'rest/config/main-local.php',
        ],
    ],
    'Live' => [
        'path' => 'prod',
        'setWritable' => [
            'rest/runtime',
            'rest/web/assets',
            'backend/runtime',
            'backend/web/assets',
        ],
        'createSessionDirectory' => [
            'backend/runtime/session',
        ],
        'setExecutable' => [
            'yii',
        ],
        'setCookieValidationKey' => [
            'backend/config/main-local.php',
            'rest/config/main-local.php',
        ],
        'setRequestBaseUrl' => [
            'backend/config/main-local.php',
            'rest/config/main-local.php',
        ],
    ],
];
