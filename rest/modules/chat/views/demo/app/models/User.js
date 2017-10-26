app.factory('User', ['$resource', 'baseUrl', function ($resource, baseUrl) {
        var User = $resource(baseUrl + 'user/:name/:action/:action_id/:subaction', {action_id: '@action_id', name: '@name'}, {
            login: {
                method: 'POST',
                params: {action: "login"},
                isArray: false,
                responseType: 'json',
            },
            logout: {
                method: 'POST',
                params: {action: "logout"},
                isArray: false,
                responseType: 'json',
            },
            current: {
                params: {action: 'current'},
                responseType: 'json',
            },
        });

        return User;
    }]);