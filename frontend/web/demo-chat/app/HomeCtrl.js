angular.module('app').controller('HomeCtrl', ['$scope', 'signPromise', 'Chat', 'Acl', '$filter', '$q',
    function ($scope, signPromise, Chat, Acl, $filter, $q) {
        $scope.user = Acl.user;
        //get channel list from server
        $scope.channels = [];           //all channels
        $scope.logs = [];               //logs
        $scope.subscribedChannels = []; //subscribed channels        
        $scope.subscriptions = {};      //centrifugo subscriptions
        $scope.message = [];            //variable for input message
        $scope.userModel = [];          //variable for input user
        $scope.model = {};              //variable for chat create
        $scope.getChannels = function () {
            Chat.getChannels({expand: 'inside,messages,users'}, function (data) {
                $scope.channels = data;
                $scope.connectToCentrifugo();
            })
        }
        //get channels at start
        $scope.getChannels();

        //sign to centrifugo
        $scope.sign = signPromise;
        $scope.sign['authHeaders'] = {"Authorization": "Bearer " + Acl.user.token};
        $scope.sign['debug'] = true;
        $scope.centrifugo = new Centrifuge(signPromise);
        //connect, after channels received
        $scope.connectToCentrifugo = function () {
            $scope.centrifugo.connect();
        }

        //create new channel
        $scope.createChannel = function () {
            if (!$scope.someForm.$invalid) {
                $scope.model.type = $scope.model.private ? 2 : 1;
                Chat.createChannel($scope.model, function (data) {
                    var channel = data;
                    $scope.getChannelData(data).then(function (data) {
                        channel.messages = data.messages;
                        channel.users = data.users;
                        $scope.channels.push(channel);
                        $scope.subscribe(channel.url);
                    });
                }, function (data) {
                    console.log(data);
                });
            }
        }

        //get messages and users
        $scope.getChannelData = function (channel) {
            var messagesPromise = Chat.getMessages({id: channel.id}).$promise;
            var userPromise = Chat.getUsers({id: channel.id}).$promise;
            return $q.all({'messages': messagesPromise, 'users': userPromise});
        }

        //join to channel
        $scope.join = function (index) {
            Chat.joinChannel({id: $scope.channels[index].id}, function (data) {
                var channel = data;
                $scope.getChannelData(data).then(function (data) {
                    channel.messages = data.messages;
                    channel.users = data.users;
                    $scope.channels[index] = channel;//update info
                    $scope.subscribe(channel.url);
                });
            })
        }
        //leave channel
        $scope.leave = function (index) {
            Chat.leaveChannel({id: $scope.channels[index].id}, function (data) {
                $scope.unsubscribe(data.url);
                //remove private/update public
                if (data.type == 2) {
                    $scope.channels.splice(index, 1);
                } else {
                    $scope.channels[index] = data;
                }
            })
        }
        //remove user from chat
        //to do: add chat admin check (pass current user role in chat)
        $scope.removeUserFromChannel = function (index, userId) {
            Chat.removeUserFromChannel({id: $scope.subscribedChannels[index].id, action_id: userId}, function () {
                var channel = $scope.subscribedChannels[index];
                $scope.getChannelData(channel).then(function (data) {
                    $scope.subscribedChannels[index].users = data.users;//update info                  
                    $scope.subscribedChannels[index].messages = data.messages;//update info                  
                });
            })
        }
        //invite user to channel
        $scope.addUserToChannel = function (index) {
            var userId = $scope.userModel[index];
            if (userId.length === 0) {
                return;
            }
            Chat.addUserToChannel({id: $scope.subscribedChannels[index].id, action_id: userId}, function (data) {
                $scope.userModel[index] = '';
                var channel = $scope.subscribedChannels[index];
                $scope.getChannelData(channel).then(function (data) {
                    $scope.subscribedChannels[index].users = data.users;//update info                  
                    $scope.subscribedChannels[index].messages = data.messages;//update info                  
                });
            })
        }
        //send message to channel
        $scope.sendMessage = function (index) {
            var message = $scope.message[index];
            if (message.length === 0) {
                return;
            }
            Chat.postMessage({id: $scope.subscribedChannels[index].id, message: message}, function (data) {
                $scope.message[index] = '';
            })
        }

        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        //subscribe to channel
        $scope.subscribe = function (url) {
            console.log('Subscribing to channel: ' + url);
            $scope.subscriptions[url] = $scope.centrifugo.subscribe(url);
            $scope.subscriptions[url].handlers = {};

            $scope.subscriptions[url].handlers.subscribe = function () {
                console.log('on subscribe', url);
                $scope.addChannelToSubscribed(url);
            }
            $scope.subscriptions[url].handlers.unsubscribe = function () {
                console.log('on unsubscribe', url);
                $scope.removeChannelFromSubscribed(url);
            }
            $scope.subscriptions[url].handlers.error = function (data) {
                $scope.addLog("ERROR : " + data.error, url);
            }
            $scope.subscriptions[url].handlers.message = function (message) {
                console.log('on subscribe message', url);
                $scope.addMessage(message, url);
            }

            $scope.subscriptions[url].on('subscribe', $scope.subscriptions[url].handlers.subscribe);
            $scope.subscriptions[url].on('unsubscribe', $scope.subscriptions[url].handlers.unsubscribe);
            $scope.subscriptions[url].on('error', $scope.subscriptions[url].handlers.error);
            $scope.subscriptions[url].on('message', $scope.subscriptions[url].handlers.message);

            $scope.subscriptions[url].presence().then(function (message) {
                //to do: create online users list. Update by join/leave
                var count = 0;
                for (var key in message.data) {
                    count++;
                }
                $scope.addLog('now connected ' + count + ' clients', url);
            }, function (err) {
                $scope.addLog('failed to obtain presence', url);
            });
            //todo move to handlers
            $scope.subscriptions[url].on('join', function (message) {
                $scope.addLog(message.data.default_info.name + ' joined channel ', url);
            });
            //todo move to handlers
            $scope.subscriptions[url].on('leave', function (message) {
                $scope.addLog(message.data.default_info.name + ' left channel ', url);
            });
        };
        //unsubscribe from channel
        $scope.unsubscribe = function (url) {
            $scope.subscriptions[url].unsubscribe();
        }
        //subscribe event handler
        $scope.addChannelToSubscribed = function (url) {
            var channels = $filter('filter')($scope.channels, {url: url}, true);
            if (channels.length == 1) {
                $scope.subscribedChannels.unshift(channels[0]);//url is unique                
                $scope.addLog("Subscribed on channel ", url);
                $scope.$apply();
            } else {
                $scope.addLog('Failed to detect channel for subscribe');
            }
        }
        //unsubscribe event handler
        $scope.removeChannelFromSubscribed = function (url) {
            var index = $scope.findSubscribedChannelIndexByUrl(url);
            if (index >= 0) {
                //remove listeners and subscription object
                $scope.subscriptions[url].off('subscribe', $scope.subscriptions[url].handlers.subscribe);
                $scope.subscriptions[url].off('unsubscribe', $scope.subscriptions[url].handlers.unsubscribe);
                $scope.subscriptions[url].off('error', $scope.subscriptions[url].handlers.error);
                $scope.subscriptions[url].off('message', $scope.subscriptions[url].handlers.message);
                delete $scope.subscriptions[url];
                //remove item
                $scope.subscribedChannels.splice(index, 1);
                $scope.addLog("Unsubscribed from channel ", url);
            } else {
                $scope.addLog('Failed to remove channel from subscribed');
            }

        }
        //subscribe to all user's avaliable channels
        $scope.subscribeToAllAvaliableChannels = function () {
            $scope.centrifugo.startAuthBatching(); //use if few private channels
            for (var channel in $scope.channels) {
                if ($scope.channels[channel].inside) {
                    $scope.subscribe($scope.channels[channel].url);
                }
            }
            $scope.centrifugo.stopAuthBatching();
        }
        //action on centrifugo connect
        $scope.centrifugo.on('connect', function () {
            $scope.addLog("connected to Centrifugo");
            $scope.subscribeToAllAvaliableChannels();
            setInterval(function () {
                // Heroku closes inactive websocket connection after 55 sec,
                // so let's send ping message periodically
                $scope.centrifugo.ping();
            }, 40000);
        });
        //action on disconnect
        $scope.centrifugo.on('disconnect', function () {
            $scope.addLog('disconnected from Centrifugo');
        });

        //add new message to channel
        $scope.addMessage = function (message, url) {
            var index = $scope.findSubscribedChannelIndexByUrl(url);
            if (index >= 0) {
                $scope.subscribedChannels[index].messages.push(message.data);
                $scope.$apply();
            }
        };
        //add log record
        $scope.addLog = function (text, channel) {
            var log = {
                channel: channel,
                text: text,
                date: $scope.viewTime()
            }
            $scope.logs.push(log);
        }

        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        //helpers
        //some example(no date)
        $scope.viewTime = function (timestamp) {
            var pad = function (n) {
                return ("0" + n).slice(-2);
            };
            var d = timestamp ? new Date(timestamp) : new Date();
            return pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
        }
        //some kind... 
        function findWithAttr(array, attr, value) {
            for (var i = 0; i < array.length; i += 1) {
                if (array[i][attr] === value) {
                    return i;
                }
            }
            return -1;
        }
        //find channel
        $scope.findSubscribedChannelIndexByUrl = function (url) {
            return findWithAttr($scope.subscribedChannels, 'url', url);
        }
    }
]);
