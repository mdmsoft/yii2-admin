var id = model.id;

$scope.rules = [];
$scope.routes = [];
Menu.values({},function(r){
    $scope.menus = r.menus;
    $scope.routes = r.routes;
});

$scope.model = model;
$scope.modelError = {};

$scope.ok = function () {
    var post = angular.copy($scope.model);
    if(post.menuParent){
        post.parent = post.menuParent.id;
        post.menuParent = undefined;
    }
    Menu.save({id:id},post,function(){
        $scope.modelError = {};
        $modalInstance.close('New menu has been added');
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