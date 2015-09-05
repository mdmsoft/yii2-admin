var $location = $injector.get('$location');
var search = $location.search();
var $filter = $injector.get('$filter');
var $modal = $injector.get('$modal');

$scope.rows = [];
$scope.q = '';

query = function () {
    Rule.query({
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

$scope.openModal = function () {
    $modal.open(angular.extend({},module.templates['/rule/form'], {
        animation: true,
        resolve: {
            item: function () {
                return {};
            }
        },
    })).result.then(function () {
        addAlert('info', 'New rule added');
        query();
    });
}

$scope.showItem = function (item) {
    $modal.open(angular.extend({},module.templates['/rule/view'], {
        animation: true,
        size:'lg',
        resolve: {
            name: function () {
                return item.name;
            }
        },
    }));
}

$scope.deleteItem = function (item) {
    if (confirm('Are you sure you want to delete?')) {
        Rule.remove({id: item.name}, {}, function () {
            addAlert('info', 'Rule deleted');
            query();
        }, function (r) {
            addAlert('error', r.statusText);
        });
    }
}

$scope.editItem = function (item) {
    $modal.open(angular.extend({},module.templates['/rule/form'], {
        animation: true,
        resolve: {
            item: function () {
                return item;
            }
        },
    })).result.then(function () {
        addAlert('info', 'New rule added');
        query();
    });
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