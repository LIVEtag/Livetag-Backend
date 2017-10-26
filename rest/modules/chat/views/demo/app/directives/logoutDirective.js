(function () {
    'use strict';
    angular.module('app').directive('logout', ['Acl', '$state', function (Acl, $state) {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    element.bind('click', function () {
                        Acl.logout();
                        $state.go('root.login');
                    });
                },
            }
        }
    ]);
})();

