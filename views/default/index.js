var prefixApiUrl = options.currentUrl + '/';

dAdmin.controller('HeaderMenuCtrl', ['$scope', '$location', function ($scope, $location) {
        $scope.headerMenu = [];
        $scope.location = $location;
        angular.forEach(options.headerMenus, function (label, id) {
            $scope.headerMenu.push({
                id:id,
                label: label,
                url: options.currentUrl + '#' + id,
            });
        });
    }]);

dAdmin.factory('Assignment', ['$resource', function ($resource) {

        return $resource(prefixApiUrl + 'assignment/:id', {}, {
            assign: {method: 'POST', url: prefixApiUrl + 'assignment/assign/:id'},
            revoke: {method: 'POST', url: prefixApiUrl + 'assignment/revoke/:id'},
        });
    }]);

dAdmin.factory('Item', ['$resource', function ($resource) {

        return $resource(prefixApiUrl + 'item/:id', {}, {
            assign: {method: 'POST', url: prefixApiUrl + 'item/assign/:id'},
            revoke: {method: 'POST', url: prefixApiUrl + 'item/revoke/:id'},
            update: {method: 'PUT'},
        });
    }]);

dAdmin.factory('Rule', ['$resource', function ($resource) {

        return $resource(prefixApiUrl + 'rule/:id', {}, {
        });
    }]);

dAdmin.factory('Route', ['$resource', function ($resource) {

        return $resource(prefixApiUrl + 'route', {}, {
            query: {method: 'GET', isArray: false},
            add: {method: 'POST', url: prefixApiUrl + 'route/add'},
            remove: {method: 'POST', url: prefixApiUrl + 'route/remove'},
        });
    }]);

dAdmin.factory('Menu', ['$resource', function ($resource) {

        return $resource(prefixApiUrl + 'menu/:id', {}, {
            values: {method: 'GET', url: prefixApiUrl + 'menu/values'}
        });
    }]);

dAdmin.filter('escape', function () {
    return window.encodeURI;
});