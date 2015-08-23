$scope.route = '';

$scope.ok = function () {
    var post = {
        items: $scope.route.split('\n'),
    };
    Route.add({}, post,
        function (r) {
            $modalInstance.close(r);
        },
        function (r) {
            window.alert(r.statusText);
        });
};

$scope.cancel = function () {
    $modalInstance.dismiss('cancel');
};