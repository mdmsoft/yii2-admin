var $location = $injector.get('$location');
var search = $location.search();
var $filter = $injector.get('$filter');
var $modal = $injector.get('$modal');

$scope.rows = [];
$scope.q = '';

query = function () {
    Menu.query({
        expand:'parentName,menuParent'
    }, function (rows) {
        $scope.rows = rows;
        $scope.filter();
    });
}
query();

// data provider
$scope.provider = {
    offset: 0,
    page: 1,
    itemPerPage: 20,
    paging: function () {
        $scope.provider.offset = ($scope.provider.page - 1) * $scope.provider.itemPerPage;
    }
};

$scope.filter = function () {
    $scope.filtered = $filter('filter')($scope.rows, $scope.q);
}

$scope.openModal = function (model) {
    $modal.open(angular.extend({},module.templates['/menu/form'], {
        animation: true,
        resolve: {
            model: function () {
                return model;
            }
        }
    })).result.then(function (r) {
        addAlert('info', r);
        query();
    });
}

$scope.deleteItem = function (item) {
    if (confirm('Are you sure you want to delete?')) {
        Menu.remove({id: item.id}, {}, function () {
            addAlert('info', 'Menu deleted');
            query();
        }, function (r) {
            addAlert('error', r.statusText);
        });
    }
}

$scope.alerts = [];
addAlert = function (type, msg) {
    var alert = {type: type, msg: msg};
    if (type == 'info') {
        alert.timeout = 3000;
    }
    $scope.alerts.push(alert);
};

$scope.closeAlert = function (index) {
    $scope.alerts.splice(index, 1);
};