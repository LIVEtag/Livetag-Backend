<?php
/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use yii\web\View;
use yii\helpers\Url;
use backend\modules\swagger\assets\SwaggerAsset;

/* @var $this \yii\web\View */
/* @var $content string */
/** @var $basePath string */
SwaggerAsset::register($this);

$jsonUrl = \Yii::$app->controller->module->params['rest.swaggerJson'];
$debugUrl = \Yii::$app->controller->module->params['rest.swaggerDebugUrl'];
$validatorUrl = \Yii::$app->controller->module->params['rest.swaggerValidatorUrl'];

$this->registerJs("window.API_BASE_URL = '" . $basePath . "';", View::POS_HEAD);
$this->registerJs("window.API_JSON_URL = '" . $basePath . '/' . $jsonUrl . "';", View::POS_HEAD);
$this->registerJs("window.VALIDATOR_URL = '" . $validatorUrl . "';", View::POS_HEAD);

?>
<div class="container swagger-section">
    <div class="row">

        <form class="form-horizontal">
            <div class="form-group">
                <label for="api-token" class="col-sm-2 control-label">Token</label>
                <div class="col-sm-10">
                    <input name="api-toke" type="text" id="api-token" placeholder="<token>" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button id="reset-token" type="reset" class="btn btn-warning">Reset</button>
                </div>
            </div>
        </form>
    </div>
    <div id="message-bar" class="swagger-ui-wrap" data-sw-translate>&nbsp;</div>
    <div id="swagger-ui-container" class="swagger-ui-wrap"></div>
</div>
