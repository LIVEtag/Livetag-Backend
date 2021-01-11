<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

/**
 * Yii bootstrap file
 */
class Yii extends \yii\BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication
     */
    public static $app;
}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = include(__DIR__ . '/../vendor/yiisoft/yii2/classes.php');
Yii::$container = new yii\di\Container;

/**
 * Class BaseApplication
 *
 * @inheritdoc
 * @property-read \common\components\streaming\Vonage $vonage
 */
abstract class BaseApplication extends yii\base\Application
{
}

/**
 * Class WebApplication
 *
 * @inheritdoc
 */
class WebApplication extends yii\web\Application
{
}

/**
 * Class ConsoleApplication
 *
 * @inheritdoc
 */
class ConsoleApplication extends yii\console\Application
{
}
