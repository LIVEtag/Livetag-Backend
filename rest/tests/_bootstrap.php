<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', __DIR__.'/../../');

require_once YII_APP_BASE_PATH . '/vendor/autoload.php';
require_once YII_APP_BASE_PATH . '/vendor/yiisoft/yii2/Yii.php';
require_once YII_APP_BASE_PATH . '/common/config/bootstrap.php';
require_once __DIR__ . '/../config/bootstrap.php';

use common\fixtures\AccessTokenFixture;
use common\fixtures\UserFixture;
use Codeception\Util\Fixtures;

ob_start(); // needed to fix "headers already sent" problem when running tests via phpstorm ui

Fixtures::add(
    'commonUserFixtures',
    [
        'users' => [
            'class' => UserFixture::class,
        ],
        'accessTokens' => [
            'class' => AccessTokenFixture::class,
        ],
    ]
);
