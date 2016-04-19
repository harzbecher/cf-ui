var example = angular.module('example', []);

example.controller('default', function($scope, $http){

    $scope.info = new Object();
    $scope.token = null;
    $scope.apps = null;
    $scope.username = null;
    $scope.password = null;

    $scope.getInfo = function(){
        $http({
            method: 'POST',
            url: 'http://localhost/cf-ui/Example/Info',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            $scope.info = res.data;
        });
    }

    $scope.getToken = function(){

        $http({
            method: 'POST',
            url: 'http://localhost/cf-ui/Example/getToken',
            data: 'username='+$scope.username+'&password='+$scope.password,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            $scope.token = res.data.access_token;
            if(!$scope.token){
                alert("Error: "+res.data.error+", "+res.data.error_description);
            }
        });
    }

    $scope.getApps = function(){
        if($scope.token === null){
            alert("A token is required");
            return false;
        }
        $http({
            method: 'POST',
            url: 'http://localhost/cf-ui/Example/listApps',
            data: 'token='+$scope.token,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            $scope.apps = res.data.resources;
        });
    }

    $scope.getInfo();


});
