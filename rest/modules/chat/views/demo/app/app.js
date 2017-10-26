var app = angular.module('app', [
    'ui.router',
    'ngResource',
    'acl'
])

/**
 * baseUrl constant
 */
app.value('baseUrl', '/rest/v1/');
/**
 * $stateChangeStart access control
 */
app.run(['$rootScope', '$state', '$stateParams', 'Acl',
    function ($rootScope, $state, $stateParams, Acl) {
        $rootScope.$state = $state;
        $rootScope.$stateParams = $stateParams;
        //UI.Router intentionally does not log console debug errors out of the box.
        $rootScope.$on("$stateChangeError", console.log.bind(console));
        //simple access control routing
        $rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
            var defaultState = ''
            if (Acl.hasRole('user')) {
                defaultState = 'root.home';
            } else {
                defaultState = 'root.login';
            }
            //check fromState - empty -> go default state
            //if fromState - DENY -> go default state
            if (!fromState.name || !Acl.can(fromState.name)) {
                fromState = defaultState;
            }
            if (!Acl.can(toState.name)) {
                event.preventDefault();
                $state.go(fromState, fromParams);
            }
        });
    }
]);
/**
 * access control
 */
app.config(['AclProvider',
    function (AclProvider) {
        AclProvider.config({
            storage: 'localStorage',
            storageKey: 'demo',
            defaultRole: 'guest',
            emptyActionDefault: false,
            defaultUser: {
                username: 'Guest'
            },
            permissions: {
                guest: {
                    actions: {
                        'root.login': true,
                    }
                },
                user: {
                    actions: {
                        'root.home': true,
                    },
                }
            },
        });
    }
]);
/**
 * simple routing
 */
app.config(['$stateProvider', '$urlRouterProvider',
    function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise(function ($injector) {
            var $state = $injector.get('$state');
            $state.go('root.home');
        });
        $stateProvider
                .state('root', {
                    abstract: true,
                    url: '',
                    template: '<ui-view autoscroll="true"/>', //empty template for same url         
                })
                .state("root.home", {
                    url: "/",
                    templateUrl: 'app/views/home.html',
                    controller: 'HomeCtrl',
                    resolve: {
                        signPromise: ['Chat', function (Chat) {
                                return Chat.sign().$promise;
                            }
                        ],
                    },
                })
                .state("root.login", {
                    url: "/login",
                    templateUrl: 'app/views/login.html',
                    controller: 'LoginCtrl',
                })
    }
]);
/**
 * Provider config
 */
app.config(['$locationProvider', '$httpProvider', '$qProvider',
    function ($locationProvider, $httpProvider, $qProvider) {
        $qProvider.errorOnUnhandledRejections(false);

        $httpProvider.defaults.transformResponse = function (data, headersGetter) {
            if (typeof (headersGetter()['content-type']) !== 'undefined' && headersGetter()['content-type'].indexOf('application/json') === 0) {
                if (data) {
                    var response = angular.fromJson(data);
                    return response.result || response;
                }
            }
            return data;
        };
        $httpProvider.interceptors.push('authInterceptor');

        $locationProvider.html5Mode(true).hashPrefix('!'); //pretty url        
    }
]);
/**
 * request settings
 */
angular.module('app').factory('authInterceptor', ['Acl', 'baseUrl', '$injector', '$q',
    function (Acl, baseUrl, $injector, $q) {
        return {
            request: function (config) {
                if (Acl.user.token && config.url.indexOf(baseUrl) == 0) {
                    config.headers['Authorization'] = "Bearer " + Acl.user.token;
                }
                return config;
            },
            responseError: function (rejection) {
                if (rejection.status === 401) {
                    var stateService = $injector.get('$state');
                    Acl.logout();
                    stateService.go('root.login');
                } else if (rejection.status === 404) {
                    console.log('PANIC 404')
                } else if (rejection.status === 500) {
                    console.log('PANIC! SERVER ERROR!', rejection);
                }
                return $q.reject(rejection);
            }
        };
    }
]);