angular.module('app').controller('LoginCtrl', ['$scope', 'User', 'Acl', '$scope', '$state', '$q',
    function ($scope, User, Acl, $scope, $state, $q) {
        $scope.errors = {};
        $scope.model = {};

        $scope.login = function () {
            $scope.errors = {};
            if (!$scope.loginForm.$invalid) {
                $scope.model.is_remember_me = 1;
                User.login($scope.model, function (data) {
                    Acl.login('user', {token: data.token});//user without data (only token)
                    User.current({expand: 'setsCount'}, function (data) {
                        var user = data.toJSON();
                        user.token = Acl.user.token;
                        Acl.login('user', user);
                        $state.go('root.home');
                    }, function (data) {
                        console.log(data)
                    });
                }, function (data) {
                    console.log(data);
                });
            }
        }
    }]);
