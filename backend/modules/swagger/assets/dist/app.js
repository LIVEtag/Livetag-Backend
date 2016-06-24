(function($) {
    $(document).ready(function($) {
        sessionStorage = window.sessionStorage;
        // Pre load translate...
        if (window.SwaggerTranslator) {
            window.SwaggerTranslator.translate();
        }
        window.swaggerUi = new SwaggerUi({
            url: window.API_JSON_URL,
            validatorUrl: window.VALIDATOR_URL,
            dom_id: "swagger-ui-container",
            supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
            onComplete: function (swaggerApi, swaggerUi) {
                if (typeof initOAuth == "function") {
                    initOAuth({
                        clientId: "your-client-id",
                        clientSecret: "your-client-secret-if-required",
                        realm: "your-realms",
                        appName: "your-app-name",
                        scopeSeparator: ",",
                        additionalQueryStringParams: {}
                    });
                }

                if (window.SwaggerTranslator) {
                    window.SwaggerTranslator.translate();
                }

                $('pre code').each(function (i, e) {
                    hljs.highlightBlock(e)
                });

                addApiKeyAuthorization();
            },
            onFailure: function (data) {
                log("Unable to Load SwaggerUI");
            },
            docExpansion: "none",
            jsonEditor: false,
            apisSorter: "alpha",
            defaultModelRendering: 'schema',
            showRequestHeaders: false
        });

        function addApiKeyAuthorization() {
            var $token = $('#api-token');
            var token = sessionStorage.getItem('token');

            if ($token.val() && $token.val() != token) {
                token = $token.val();
            }

            if(token && token.trim() != "") {
                var apiKeyAuth = new SwaggerClient.ApiKeyAuthorization(
                    "Authorization",
                    "Bearer " + encodeURIComponent(token), "header"
                );
                sessionStorage.setItem('token', token);
                $token.val(token);
                window.swaggerUi.api.clientAuthorizations.add("bearer", apiKeyAuth);
                log("Set bearer token: " + token);
            }
        }

        $('#api-token').change(function () {
            addApiKeyAuthorization();
        });

        $('#reset-token').click(function () {
            sessionStorage.clear();
        });

        window.swaggerUi.load();

        function log() {
            if ('console' in window) {
                console.log.apply(console, arguments);
            }
        }
    });
})(jQuery);
