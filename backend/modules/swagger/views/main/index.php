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
$historyUrl = \Yii::$app->controller->module->params['rest.swaggerHistory'];

$this->registerJs("window.API_HISTORY_URL = '" . $basePath . '/' . $historyUrl . "';", View::POS_HEAD);
$this->registerJs("window.API_JSON_URL = '" . $basePath . '/' . $jsonUrl . "';", View::POS_HEAD);

$js = <<<JS
$(function () {
    url = window.API_JSON_URL;

    // Pre load translate...
    if(window.SwaggerTranslator) {
      window.SwaggerTranslator.translate();
    }
    window.swaggerUi = new SwaggerUi({
      //validatorUrl : 'http://localhost:3000',
      url: url,
      dom_id: "swagger-ui-container",
      supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
      onComplete: function(swaggerApi, swaggerUi){
        if(typeof initOAuth == "function") {
          initOAuth({
            clientId: "your-client-id",
            clientSecret: "your-client-secret-if-required",
            realm: "your-realms",
            appName: "your-app-name",
            scopeSeparator: ",",
            additionalQueryStringParams: {}
          });
        }

        if(window.SwaggerTranslator) {
          window.SwaggerTranslator.translate();
        }

        $('pre code').each(function(i, e) {
          hljs.highlightBlock(e)
        });

        addApiKeyAuthorization();
      },
      onFailure: function(data) {
        log("Unable to Load SwaggerUI");
      },
      docExpansion: "none",
      jsonEditor: false,
      apisSorter: "alpha",
      defaultModelRendering: 'schema',
      showRequestHeaders: true,
    });

    function addApiKeyAuthorization(){
      var key = encodeURIComponent( $('#input_apiKey')[0].value );
      if(key && key.trim() != "") {
          var apiKeyAuth = new SwaggerClient.ApiKeyAuthorization( "Authorization", "Bearer " + key, "header" );
          window.swaggerUi.api.clientAuthorizations.add( "bearer", apiKeyAuth );
          log( "Set bearer token: " + key );
      }
    }

    $('#input_apiKey').change(addApiKeyAuthorization);

    window.swaggerUi.load();

    function log() {
      if ('console' in window) {
        console.log.apply(console, arguments);
      }
    }
});
JS;

$this->registerJs($js, View::POS_END);

?>
<div id='header'>
    <div class="swagger-ui-wrap">
        <a target="_blank" id="logo" href="http://swagger.io">swagger</a>
        <form id='api_selector'>
            <div class='input'>
                <input value="http://rest.yii2-base.loc/"
                       placeholder="http://rest.yii2-base.loc/"
                       id="input_baseUrl"
                       name="baseUrl"
                       type="text"/>
            </div>
            <div class='input'>
                <input placeholder="Authorization" id="input_apiKey" name="apiKey" type="text"/>
            </div>
            <div class='input'>
                <a id="explore" href="#" data-sw-translate>Explore</a>
            </div>
        </form>
    </div>
</div>

<div id="message-bar" class="swagger-ui-wrap" data-sw-translate>&nbsp;</div>
<div id="swagger-ui-container" class="swagger-ui-wrap"></div>

<!-- API history block -->
<div class="swagger-ui-wrap log-wrap">
    <h2>Short API History &mdash; last 10 swagger commits</h2>
    <div id="apihistory" class="wrap-log"></div>
</div> <!-- End of API history block -->
