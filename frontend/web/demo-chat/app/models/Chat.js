app.factory('Chat', ['$resource', 'baseUrl', function ($resource, baseUrl) {
        var Chat = $resource(baseUrl + 'channel/:id/:action/:action_id', {action_id: '@action_id', id: '@id'}, {
            //'POST auth'
            auth: {
                method: 'POST',
                params: {action: 'auth'},
                responseType: 'json',
            },
            //'GET sign'
            sign: {
                method: 'POST',
                params: {action: 'sign'},
                responseType: 'json',
            },
            //GET <id:\d+>
            getChannel: {
                method: 'GET',
                responseType: 'json',
            },
            //'POST'
            createChannel: {
                method: 'POST',
                params: {expand: 'inside'}, 
                responseType: 'json',
            },
            //GET
            getChannels: {
                method: 'GET',
                params: {},
                isArray: true,
                responseType: 'json',
            },
            //'PUT <id:\d+>/join'
            joinChannel: {
                method: 'PUT',
                params: {action: 'join', expand: 'inside'}, //id required
                responseType: 'json',
            },
            //'PUT <id:\d+>/leave' 
            leaveChannel: {
                method: 'PUT',
                params: {action: 'leave', expand: 'inside'}, //id required
                responseType: 'json',
            },
            //'GET <id:\d+>/message'
            getMessages: {
                method: 'GET',
                isArray: true,
                params: {action: 'message'}, //id required
                responseType: 'json',
            },
            //'POST <id:\d+>/message'
            postMessage: {
                method: 'POST',
                params: {action: 'message'}, //id required
                responseType: 'json',
            },
            //'GET <id:\d+>/user'
            getUsers: {
                method: 'GET',
                isArray: true,
                params: {action: 'user'}, //id required
                responseType: 'json',
            },
            //'POST <id:\d+>/user/<userId:\d+>'
            addUserToChannel: {
                method: 'POST',
                params: {action: 'user'}, //id|action_id required
                responseType: 'json',
            },
            // 'DELETE <id:\d+>/user/<userId:\d+>'
            removeUserFromChannel: {
                method: 'DELETE',
                params: {action: 'user'}, //id|action_id required,
                responseType: 'json',
            },
        });
        return Chat;
    }]);