<?php

/**
 * Copyright © 2016 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use yii\web\View;
use yii\helpers\Url;
use backend\modules\swagger\assets\SwaggerAsset;
use Yii;

/* @var $this \yii\web\View */
/* @var $content string */
/** @var $basePath string */
SwaggerAsset::register($this);

$jsonUrl = \Yii::$app->controller->module->params['rest.swaggerJson'];
$debugUrl = \Yii::$app->controller->module->params['rest.swaggerDebugUrl'];
$validatorUrl = \Yii::$app->controller->module->params['rest.swaggerValidatorUrl'];

$this->registerJs("window.API_JSON_URL = '" . $basePath . '/' . $jsonUrl . "';", View::POS_HEAD);

$js = <<<JS
jQuery.browser = jQuery.browser || {};
(function () {
    jQuery.browser.msie = jQuery.browser.msie || false;
    jQuery.browser.version = jQuery.browser.version || 0;
    if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
        jQuery.browser.msie = true;
        jQuery.browser.version = RegExp.$1;
    }
})();

$(function () {
    var url = window.location.search.match(/url=([^&]+)/);
    if (url && url.length > 1) {
      url = decodeURIComponent(url[1]);
    } else {
      url = window.API_JSON_URL;
    }

    // Pre load translate...
    if(window.SwaggerTranslator) {
      window.SwaggerTranslator.translate();
    }
    window.swaggerUi = new SwaggerUi({
      url: url,
      dom_id: "swagger-ui-container",
      supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
      onComplete: function(swaggerApi, swaggerUi){
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

    $('body').on('change', '#input_apiKey', addApiKeyAuthorization);
    window.swaggerUi.load();

    function log() {
      if ('console' in window) {
        console.log.apply(console, arguments);
      }
    }

     $('#input_baseUrl').val(url);
});
$(function () {

        $(window).scroll(function () {
            var sticky = $(".sticky-nav");

            i(sticky);
            r(sticky);

            function n() {
                return window.matchMedia("(min-width: 992px)").matches
            }

            function e() {
                n() ?
                    sticky.parents(".sticky-nav-placeholder").removeAttr("style") :
                    sticky.parents(".sticky-nav-placeholder").css("min-height", sticky.outerHeight())
            }

            function i(n) {
                n.hasClass("fixed") || (navOffset = n.offset().top);
                e();
                $(window).scrollTop() > navOffset ?
                    $(".modal.in").length || n.addClass("fixed") :
                    n.removeClass("fixed")
            }

            function r(e) {
                function i() {
                    var i = $(window).scrollTop(), r = e.parents(".sticky-nav");
                    return r.hasClass("fixed") && !n() && (i = i + r.outerHeight() + 40), i
                }

                function r(e) {
                    var t = o.next("[data-endpoint]"), n = o.prev("[data-endpoint]");
                    return "next" === e ?
                        t.length ? t : o.parent().next().find("[data-endpoint]").first() :
                        "prev" === e ? n.length ? n : o.parent().prev().find("[data-endpoint]").last() : []
                }

                var nav = e.find("[data-navigator]");
                if (nav.find("[data-endpoint][data-selected]").length) {
                    var o = nav.find("[data-endpoint][data-selected]"),
                        a = $("#" + o.attr("data-endpoint")),
                        s = a.offset().top,
                        l = (s + a.outerHeight(), r("next")),
                        u = r("prev");
                    if (l.length) {
                        {
                            var d = $("#" + l.attr("data-endpoint")), f = d.offset().top;
                            f + d.outerHeight()
                        }
                        i() >= f && c(l)
                    }
                    if (u.length) {
                        var p = $("#" + u.attr("data-endpoint")),
                        g = u.offset().top;
                        v = (g + p.outerHeight(), 100);
                        i() < s - v && c(u)
                    }
                }
            }

            function s() {
                var e = $(".sticky-nav [data-navigator]"),
                    n = e.find("[data-endpoint]").first();
                n.attr("data-selected", "");
                u.find("[data-selected-value]").html(n.text())
            }

            function c(e) {
                {
                    var n = $(".sticky-nav [data-navigator]");
                    $("#" + e.attr("data-endpoint"))
                }
                n.find("[data-resource]").removeClass("active");
                n.find("[data-selected]").removeAttr("data-selected");
                e.closest("[data-resource]").addClass("active");
                e.attr("data-selected", "");
                sticky.find("[data-selected-value]").html(e.text())
            }
        });

    });
    $(function () {
        $("[data-toggle='tooltip']").tooltip();
    });
    
    (function ($) {

        var defaults = {
            waggle : 5,
            duration : 2,
            interval : 200
        };

        function rand(waggle) {
            return Math.random() % (waggle - (waggle / 2) + 1) + (waggle / 2);
        }

        $.fn.wiggle = function (options, callback) {
            options = $.extend({}, defaults, options);

            var duration = options.duration,
                elem = this,
                moveLeft = false,
                left = elem.css('left'),
                pos = elem.css('position'),
                timer;
            elem.css('position', 'relative');

            function doWiggle() {
                var move = rand(options.waggle);
                elem.animate({
                    left : moveLeft ? move : -move
                }, options.interval);
                moveLeft = !moveLeft;

                if (options.wiggleCallback) {
                    options.wiggleCallback(elem);
                }

                duration -= options.interval / 1000;
                if (duration <= 0) {
                    elem.css('left', left);
                    elem.css('position', pos);

                    clearTimeout(timer);

                    callback && callback();
                } else {
                    timer = setTimeout(doWiggle, options.interval);
                }
            }
            timer = setTimeout(doWiggle, options.interval);
        };

    })(window.jQuery);
JS;
$this->registerJs($js, View::POS_END);
?>
<header class="site-header">
    <nav role="navigation" class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <button type="button" data-toggle="collapse" data-target="#navbar-collapse" class="navbar-toggle">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <h1 class="navbar-brand">
                    <a href="http://swagger.io">
                        <span>swagger explorer</span>
                    </a>
                </h1>
            </div>
            <div id="navbar-collapse" class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-left">
                    <li class="li-why">
                        <a href="http://swagger.io" style="font-size: 25px; padding-left: 0px">Swagger explorer</a>
                    </li>
                </ul>
                <span style="float:right">
                    <a href="<?= $debugUrl ?>">
                        <img id="validator" src="<?= $validatorUrl ?>">
                    </a>
                </span>
            </div>
        </div>
    </nav>
</header>

<section class="content">
    <div id="api2-explorer">
        <div class="swagger-section page-docs" style="zoom: 1">
            <div class="main-section">
                <div id="swagger-ui-container" class="swagger-ui-wrap">
                </div>
            </div>
        </div>
    </div>
</section>
