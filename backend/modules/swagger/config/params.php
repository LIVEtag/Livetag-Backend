<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

return [
    'rest.swaggerJson' => 'swagger/main/json',
    'rest.swaggerDebugUrl' => 'http://' . parse_url(\yii\helpers\Url::home(true))['host'] . ':8080/debug?url=http://gbksoftyiidevelop.my/rest/web/swagger/main/json',
    'rest.swaggerValidatorUrl' => 'http://' . parse_url(\yii\helpers\Url::home(true))['host'] . ':8080/validate?url=http://gbksoftyiidevelop.my/rest/web/swagger/main/json',
];
