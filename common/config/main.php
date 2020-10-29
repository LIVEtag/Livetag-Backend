<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

use common\components\validation\validators as RestValidators;

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'UTC',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
        ],
    ],
    'container' => [
        'singletons' => [
            \common\components\validation\ErrorListInterface::class => \common\components\validation\ErrorList::class,
        ],
        'definitions' => [
            \yii\validators\StringValidator::class => RestValidators\StringValidator::class,
            \yii\validators\EmailValidator::class => RestValidators\EmailValidator::class,
            \yii\validators\FileValidator::class => RestValidators\FileValidator::class,
            \yii\validators\ImageValidator::class => RestValidators\ImageValidator::class,
            \yii\validators\BooleanValidator::class => RestValidators\BooleanValidator::class,
            \yii\validators\NumberValidator::class => RestValidators\NumberValidator::class,
            \yii\validators\DateValidator::class => RestValidators\DateValidator::class,
            \yii\validators\RangeValidator::class => RestValidators\RangeValidator::class,
            \yii\validators\RequiredValidator::class => RestValidators\RequiredValidator::class,
            \yii\validators\RegularExpressionValidator::class => RestValidators\RegularExpressionValidator::class,
            \yii\validators\UrlValidator::class => RestValidators\UrlValidator::class,
            \yii\validators\CompareValidator::class => RestValidators\CompareValidator::class,
            \yii\validators\IpValidator::class => RestValidators\IpValidator::class,
            \yii\validators\UniqueValidator::class => RestValidators\UniqueValidator::class,
            \yii\validators\ExistValidator::class => RestValidators\ExistValidator::class,
        ],
    ],
];
