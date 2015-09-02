var $location = $injector.get('$location');
var $pageInfo = $injector.get('$pageInfo');

$scope.q = '';

var onSearch = false;
var panding = false;

query = function () {
    onSearch = true;
    Assignment.query({
        q:$scope.q,
        expand: 'assignments',
    }, function (rows, headerCallback) {
        $pageInfo(headerCallback, $scope.provider);
        $scope.rows = rows;
        onSearch = false;
        if(panding){
            $scope.search();
        }
    },function (){
        onSearch = false;
        if(panding){
            $scope.search();
        }
    });
}
query();

// data provider
$scope.provider = {
    paging: function () {
        query();
    }
};

$scope.search = function(){
    if(!onSearch){
        panding = false;
        query();
    }else{
        panding = true;
    }
}
