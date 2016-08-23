#!/usr/bin/env php
<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

// @see https://github.com/sebastianbergmann/phpcpd/issues/18
ini_set('mbstring.func_overload', 0);
if (ini_get('mbstring.internal_encoding')) {
    ini_set('mbstring.internal_encoding', NULL);
}

$loaded = false;
$file = __DIR__ . '/../../../../vendor/autoload.php';

if (file_exists($file)) {
    require_once $file;
    $loaded = true;
}

if (!$loaded) {
    die(
        'You need to set up the project dependencies using the following commands:' . PHP_EOL .
        'wget http://getcomposer.org/composer.phar' . PHP_EOL .
        'php composer.phar install' . PHP_EOL
    );
}

$application = new \dev\lib\phpcpd\cli\Application();
$application->run();
