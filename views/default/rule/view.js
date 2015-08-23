$sce = $injector.get('$sce');

$scope.name = name;
Rule.get({
    id: name,
    expand: 'content'
}, function (r) {
    $scope.content = $sce.trustAsHtml(r.content);
});

$scope.close = function () {
    $modalInstance.dismiss('cancel');
}