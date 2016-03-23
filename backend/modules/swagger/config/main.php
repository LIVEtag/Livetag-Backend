<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

Yii::setAlias('@swagger', dirname(__DIR__));

$params = array_merge(
    require(__DIR__ . '/params.php')
);

return [
    'params' => $params,
];
