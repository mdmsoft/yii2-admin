var prefixApiUrl = options.currentUrl + '/';

module.run(['$rootScope', '$location', function ($scope, $location) {
        $scope.headerMenu = [];
        $scope.isRouteActive = function (id){
            return $location.path().indexOf(id)===0;
        };
        angular.forEach(options.headerMenus, function (label, id) {
            $scope.headerMenu.push({
                id:id,
                label: label,
                url: options.currentUrl + '#' + id,
            });
        });
    }]);

module.factory('Assignment', ['$resource', function ($resource) {

        return $resource(prefixApiUrl + 'assignment/:id', {}, {
            assign: {method: 'POST', url: prefixApiUrl + 'assignment/assign/:id'},
            revoke: {method: 'POST', url: prefixApiUrl + 'assignment/revoke/:id'},
        });
    }]);

module.factory('Item', ['$resource', function ($resource) {

        return $resource(prefixApiUrl + 'item/:id', {}, {
            assign: {method: 'POST', url: prefixApiUrl + 'item/assign/:id'},
            revoke: {method: 'POST', url: prefixApiUrl + 'item/revoke/:id'},
            update: {method: 'PUT'},
        });
    }]);

module.factory('Rule', ['$resource', function ($resource) {

        return $resource(prefixApiUrl + 'rule/:id', {}, {
        });
    }]);

module.factory('Route', ['$resource', function ($resource) {

        return $resource(prefixApiUrl + 'route', {}, {
            query: {method: 'GET', isArray: false},
            add: {method: 'POST', url: prefixApiUrl + 'route/add'},
            remove: {method: 'POST', url: prefixApiUrl + 'route/remove'},
        });
    }]);

module.factory('Menu', ['$resource', function ($resource) {

        return $resource(prefixApiUrl + 'menu/:id', {}, {
            values: {method: 'GET', url: prefixApiUrl + 'menu/values'}
        });
    }]);

module.filter('escape', function () {
    return window.encodeURI;
});