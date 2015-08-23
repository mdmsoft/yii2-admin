
dAdmin.factory('Assignment', ['$resource', function ($resource) {

        return $resource(options.prefixUrl + 'assignment/:id', {}, {
            assign: {method: 'POST', url: options.prefixUrl + 'assignment/assign/:id'},
            revoke: {method: 'POST', url: options.prefixUrl + 'assignment/revoke/:id'},
        });
    }]);

dAdmin.factory('Item', ['$resource', function ($resource) {

        return $resource(options.prefixUrl + 'item/:id', {}, {
            assign: {method: 'POST', url: options.prefixUrl + 'item/assign/:id'},
            revoke: {method: 'POST', url: options.prefixUrl + 'item/revoke/:id'},
            update: {method: 'PUT'},
        });
    }]);

dAdmin.factory('Rule', ['$resource', function ($resource) {

        return $resource(options.prefixUrl + 'rule/:id', {}, {
        });
    }]);

dAdmin.factory('Route', ['$resource', function ($resource) {

        return $resource(options.prefixUrl + 'route/:id', {}, {
            query: {method: 'GET', isArray: false},
            add: {method: 'POST'},
        });
    }]);

dAdmin.factory('Menu', ['$resource', function ($resource) {

        return $resource(options.prefixUrl + 'menu/:id', {}, {
            values: {method: 'GET', url: options.prefixUrl + 'menu/values'}
        });
    }]);

dAdmin.filter('escape', function () {
    return window.encodeURI;
});