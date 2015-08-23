
var oldName = item.name;
$scope.model = {
    name:item.name,
    className:item.className,
};
$scope.modelError = {};

$scope.ok = function () {
    Rule.save({id:oldName},$scope.model,function(r){
        $scope.modelError = {};
        $modalInstance.close(r);
    },function(r){
        if (r.status == 422) {
            angular.forEach(r.data,function(err){
                $scope.modelError[err.field] = err.message;
            });
        }else{
            $scope.statusText = r.statusText;
        }
    });
};

$scope.cancel = function () {
    $modalInstance.dismiss('cancel');
};

$scope.closeAlert = function(){
    delete $scope.statusText;
}