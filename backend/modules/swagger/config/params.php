<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

return [
    'rest.swaggerJson' => 'swagger/main/json',
    'rest.swaggerDebugUrl' => 'http://' . parse_url('http://' . Yii::getAlias('@backend.domain'))['host']
        . ':8080/debug?url=http://' . Yii::getAlias('@rest.domain') . '/swagger/main/json',
    'rest.swaggerValidatorUrl' => 'http://' . parse_url('http://' . Yii::getAlias('@backend.domain'))['host']
        . ':8080/validate?url=http://' . Yii::getAlias('@rest.domain') . '/swagger/main/json',
];
