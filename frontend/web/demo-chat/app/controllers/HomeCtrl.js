(function () {
    'use strict';
    angular.module('app').controller('HomeCtrl', ['$scope', 'signPromise', 'Chat', 'Acl', '$filter', '$q',
        function ($scope, signPromise, Chat, Acl, $filter, $q) {
            $scope.user = Acl.user;
            $scope.logs = [];               //logs
            $scope.channels = [];           //all channels
            $scope.subscribedChannels = []; //subscribed channels        
            $scope.subscriptions = {};      //centrifugo subscriptions
            $scope.message = [];            //variable for input message
            $scope.userModel = [];          //variable for input user
            $scope.model = {};              //variable for chat create
            /**
             * get channel list from server
             */
            $scope.getChannels = function () {
                Chat.getChannels({expand: 'inside,messages,users'}, function (data) {
                    $scope.channels = data;
                    $scope.connectToCentrifugo();
                });
            };
            //get channels at start
            $scope.getChannels();

            //sign to centrifugo
            $scope.sign = signPromise;
            $scope.sign['authHeaders'] = {"Authorization": "Bearer " + Acl.user.token};
            $scope.sign['refreshHeaders'] = {"Authorization": "Bearer " + Acl.user.token};
            $scope.sign['debug'] = true;
            $scope.centrifugo = new Centrifuge(signPromise);
            //connect, after channels received
            $scope.connectToCentrifugo = function () {
                $scope.centrifugo.connect();
            };

            /**
             * create new channel         
             */
            $scope.createChannel = function () {
                if (!$scope.someForm.$invalid) {
                    $scope.model.type = $scope.model.private ? 2 : 1;
                    Chat.createChannel($scope.model, function (data) {
                        var channel = data;
                        $scope.getChannelData(data.id).then(function (data) {
                            channel.messages = data.messages;
                            channel.users = data.users;
                            $scope.channels.push(channel);
                            $scope.subscribe(channel.url);
                        });
                    }, function (data) {
                        console.log(data);
                    });
                }
            };

            /**
             * get users in channel
             * @param {Number} channelId         
             */
            $scope.getChannelUserData = function (channelId) {
                return Chat.getUsers({id: channelId}).$promise;
            };

            /**
             * get channel additional data (messages and users)
             * @param {Number} channelId        
             */
            $scope.getChannelData = function (channelId) {
                var messagesPromise = Chat.getMessages({id: channelId}).$promise;
                var userPromise = $scope.getChannelUserData(channelId);
                return $q.all({'messages': messagesPromise, 'users': userPromise});
            };

            /**
             * join to channel
             * @param {Number} index         
             */
            $scope.join = function (index) {
                Chat.joinChannel({id: $scope.channels[index].id}, function (data) {
                    var channel = data;
                    $scope.getChannelData(channel.id).then(function (data) {
                        channel.messages = data.messages;
                        channel.users = data.users;
                        $scope.channels[index] = channel;//update info
                        $scope.subscribe(channel.url);
                    });
                });
            };
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
                });
            };

            /**
             * check if selected user online
             * @param {Number} index subscribedChannels index
             * @param {Number} userId
             * @returns {Boolean}
             */
            $scope.isOnine = function (index, userId) {
                var url = $scope.subscribedChannels[index].url;
                return $scope.subscriptions[url].users.indexOf(userId) >= 0 ? true : false;
            };

            /**
             * remove user from chat (api)
             * @todo add chat admin check (pass current user role in chat)
             * @todo issue: there are no way to kick user from channel - we could only deny to write message to it...
             * @param {Number} index
             * @param {Number} userId         
             */
            $scope.removeUserFromChannel = function (index, userId) {
                Chat.removeUserFromChannel({id: $scope.subscribedChannels[index].id, action_id: userId}, function () {
                    var channel = $scope.subscribedChannels[index];
                    $scope.getChannelData(channel.id).then(function (data) {
                        $scope.subscribedChannels[index].users = data.users;//update info                  
                        $scope.subscribedChannels[index].messages = data.messages;//update info                  
                    });
                });
            };
            /**
             * invite user to channel (api)
             * @param {Number} index
             */
            $scope.addUserToChannel = function (index) {
                var userId = $scope.userModel[index];
                if (userId.length === 0) {
                    return;
                }
                Chat.addUserToChannel({id: $scope.subscribedChannels[index].id, action_id: userId}, function (data) {
                    $scope.userModel[index] = '';
                    var channel = $scope.subscribedChannels[index];
                    $scope.getChannelData(channel.id).then(function (data) {
                        $scope.subscribedChannels[index].users = data.users;//update info                  
                        $scope.subscribedChannels[index].messages = data.messages;//update info                  
                    });
                });
            };
            //
            /**
             * send message to channel (api)
             * @param {Number} index
             */
            $scope.sendMessage = function (index) {
                var message = $scope.message[index];
                if (message.length === 0) {
                    return;
                }
                Chat.postMessage({id: $scope.subscribedChannels[index].id, message: message}, function (data) {
                    $scope.message[index] = '';
                });
            };

            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////

            /**
             * create handlers and create listeners
             * @param {String} url
             */
            $scope.addSubscribeListeners = function (url) {
                $scope.subscriptions[url].handlers = {};

                $scope.subscriptions[url].handlers.subscribe = function () {
                    $scope.addChannelToSubscribed(url);
                    $scope.$apply();
                };
                $scope.subscriptions[url].handlers.unsubscribe = function () {
                    $scope.removeChannelFromSubscribed(url);
                };
                $scope.subscriptions[url].handlers.error = function (data) {
                    $scope.addLog("ERROR : " + data.error, url);
                    $scope.$apply();
                };
                $scope.subscriptions[url].handlers.message = function (message) {
                    $scope.addMessage(message, url);
                };
                $scope.subscriptions[url].handlers.join = function (message) {
                    var index = $scope.findSubscribedChannelIndexByUrl(url);
                    //in this way this not fire on init joins (subscribedChannels not filled yet
                    if (index >= 0) {
                        var userId = Number(message.data.user);
                        //if added user not present in list - update user list
                        if ($scope.isUserExistInArray($scope.subscribedChannels[index].users, userId) < 0) {
                            $scope.getChannelUserData($scope.subscribedChannels[index].id).then(function (data) {
                                $scope.subscribedChannels[index].users = data;
                            });
                        }
                    }
                    //update online list
                    $scope.addUserToOnlineInChannel(url, message.data.user);
                    $scope.addLog(message.data.default_info.name + ' joined channel ', message.channel);
                    $scope.$apply();
                };
                $scope.subscriptions[url].handlers.leave = function (message) {
                    var index = $scope.findSubscribedChannelIndexByUrl(url);
                    //in this way this not fire on init joins (subscribedChannels not filled yet
                    if (index >= 0) {
                        //there are no way to detect if user just go offline or leave the channel totally
                        //so, right now, update user info every time
                        $scope.getChannelUserData($scope.subscribedChannels[index].id).then(function (data) {
                            $scope.subscribedChannels[index].users = data;
                        });
                    }
                    $scope.removeUserFromOnlineInChannel(url, message.data.user);
                    $scope.addLog(message.data.default_info.name + ' left channel ', message.channel);
                    $scope.$apply();
                };

                $scope.subscriptions[url].on('subscribe', $scope.subscriptions[url].handlers.subscribe);
                $scope.subscriptions[url].on('unsubscribe', $scope.subscriptions[url].handlers.unsubscribe);
                $scope.subscriptions[url].on('error', $scope.subscriptions[url].handlers.error);
                $scope.subscriptions[url].on('message', $scope.subscriptions[url].handlers.message);
                $scope.subscriptions[url].on('join', $scope.subscriptions[url].handlers.join);
                $scope.subscriptions[url].on('leave', $scope.subscriptions[url].handlers.leave);
            };

            /**
             * remove listeners and subscribe object
             * @param {String} url
             */
            $scope.removeSubscribeListeners = function (url) {
                $scope.subscriptions[url].off('subscribe', $scope.subscriptions[url].handlers.subscribe);
                $scope.subscriptions[url].off('unsubscribe', $scope.subscriptions[url].handlers.unsubscribe);
                $scope.subscriptions[url].off('error', $scope.subscriptions[url].handlers.error);
                $scope.subscriptions[url].off('message', $scope.subscriptions[url].handlers.message);
                $scope.subscriptions[url].off('join', $scope.subscriptions[url].handlers.join);
                $scope.subscriptions[url].off('leave', $scope.subscriptions[url].handlers.leave);
                delete $scope.subscriptions[url];
            }

            /**
             * Subscribe to selected channel
             * @param {String} url         
             */
            $scope.subscribe = function (url) {
                $scope.subscriptions[url] = $scope.centrifugo.subscribe(url);
                $scope.subscriptions[url].users = [];//add place for online users store
                $scope.addSubscribeListeners(url);
                //get presence info
                $scope.subscriptions[url].presence().then(function (message) {
                    var count = 0;
                    for (var key in message.data) {
                        $scope.addUserToOnlineInChannel(url, message.data[key].user);
                        count++;
                    }
                    $scope.addLog('now connected ' + count + ' clients', url);
                    $scope.$apply();
                }, function (err) {
                    $scope.addLog('failed to obtain presence', url);
                });
            };

            /**
             * unsubscribe from channel
             * @param {String} url
             */
            $scope.unsubscribe = function (url) {
                $scope.subscriptions[url].unsubscribe();
            };

            /**
             * add user to online list in channel
             * @param {String} url
             * @param {Number} userId         
             */
            $scope.addUserToOnlineInChannel = function (url, userId) {
                userId = Number(userId);
                var index = $scope.subscriptions[url].users.indexOf(userId);
                if (index < 0) {
                    $scope.subscriptions[url].users.push(userId);
                }
            };

            /**
             * remove user from online list in channel
             * @param {String} url
             * @param {Number} userId         
             */
            $scope.removeUserFromOnlineInChannel = function (url, userId) {
                var index = $scope.subscriptions[url].users.indexOf(Number(userId))
                if (index >= 0) {
                    $scope.subscriptions[url].users.splice(index, 1);
                }
            };

            /**
             * subscribe event handler
             * @param {String} url
             * @returns {undefined}
             */
            $scope.addChannelToSubscribed = function (url) {
                var channels = $filter('filter')($scope.channels, {url: url}, true);
                if (channels.length === 1) {
                    $scope.subscribedChannels.unshift(channels[0]);//url is unique                
                    $scope.addLog("Subscribed on channel ", url);
                } else {
                    $scope.addLog('Failed to detect channel for subscribe');
                }
            };


            /**
             * unsubscribe event handler
             * @param {String} url         
             */
            $scope.removeChannelFromSubscribed = function (url) {
                var index = $scope.findSubscribedChannelIndexByUrl(url);
                if (index >= 0) {
                    //remove listeners and subscription object
                    $scope.removeSubscribeListeners(url);
                    //remove item from subscribed channels
                    $scope.subscribedChannels.splice(index, 1);
                    $scope.addLog("Unsubscribed from channel ", url);
                } else {
                    $scope.addLog('Failed to remove channel from subscribed');
                }
            };

            /**
             * subscribe to all user's avaliable channels      
             */
            $scope.subscribeToAllAvaliableChannels = function () {
                $scope.centrifugo.startAuthBatching(); //use if few private channels
                for (var channel in $scope.channels) {
                    if ($scope.channels[channel].inside) {
                        $scope.subscribe($scope.channels[channel].url);
                    }
                }
                $scope.centrifugo.stopAuthBatching();
            }

            /**
             * action on centrifugo connect
             */
            $scope.centrifugo.on('connect', function () {
                $scope.addLog("connected to Centrifugo");
                $scope.subscribeToAllAvaliableChannels();
                setInterval(function () {
                    // Heroku closes inactive websocket connection after 55 sec,
                    // so let's send ping message periodically
                    $scope.centrifugo.ping();
                }, 40000);
            });
            /**
             * action on disconnect
             */
            $scope.centrifugo.on('disconnect', function () {
                $scope.addLog('disconnected from Centrifugo');
            });

            /**
             * Add new message to channel
             * @param {String} message
             * @param {String} url
             */
            $scope.addMessage = function (message, url) {
                var index = $scope.findSubscribedChannelIndexByUrl(url);
                if (index >= 0) {
                    $scope.subscribedChannels[index].messages.push(message.data);
                    $scope.$apply();
                }
            };

            /**
             * Add log record
             * @param {String} text 
             * @param {String} channel         
             */
            $scope.addLog = function (text, channel) {
                var log = {
                    channel: channel,
                    text: text,
                    date: $scope.viewTime()
                };
                $scope.logs.push(log);
            };

            ////////////////////////////////////////////////////////////////////////
            //////////////////////////helpers///////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            /**
             * example message time (no date)
             * @param {Number} timestamp
             * @returns {String}
             */
            $scope.viewTime = function (timestamp) {
                var pad = function (n) {
                    return ("0" + n).slice(-2);
                };
                var d = timestamp ? new Date(timestamp) : new Date();
                return pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
            };

            /**
             * search in array by field value
             * @param {Array} array
             * @param {String} attr
             * @param {String} value
             * @returns {Number}
             */
            function findWithAttr(array, attr, value) {
                for (var i = 0; i < array.length; i += 1) {
                    if (array[i][attr] === value) {
                        return i;
                    }
                }
                return -1;
            }

            /**
             * Find channel by url
             * @param {String} url
             * @returns {Number}
             */
            $scope.findSubscribedChannelIndexByUrl = function (url) {
                return findWithAttr($scope.subscribedChannels, 'url', url);
            };

            /**
             * search in array by field
             * @param {Array} array
             * @param {Number} userId
             * @returns {Number}
             */
            $scope.isUserExistInArray = function (array, userId) {
                for (var i = 0; i < array.length; i += 1) {
                    if (array[i].user.id === userId) {
                        return i;
                    }
                }
                return -1;
            };
        }
    ]);
})();
