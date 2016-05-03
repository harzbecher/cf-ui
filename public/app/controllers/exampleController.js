var example = angular.module('example', []);

example.controller('default', function($scope, $http){

    $scope.info = new Object();
    $scope.token = null;

    $scope.username = null;
    $scope.password = null;

    $scope.spaces = null;
    $scope.apps = null;
    $scope.services= null;

    $scope.getInfo = function(){
        $http({
            method: 'POST',
            url: '/cf-ui/General/Info',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            $scope.info = res.data;
        });
    }

    $scope.getToken = function(){

        $http({
            method: 'POST',
            url: '/cf-ui/General/getToken',
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
            url: '/cf-ui/Apps/listApps',
            data: 'token=' + $scope.token + '&spaceguid=' + $scope.spaceguid,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            $scope.apps = res.data.resources;
        });
    }

    $scope.getSpaces = function(){
        if($scope.token === null){
            alert("A token is required");
            return false;
        }
        $http({
            method: 'POST',
            url: '/cf-ui/Spaces/listSpaces',
            data: 'token=' + $scope.token ,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            $scope.spaces = res.data.resources;
        });
    }

    $scope.createApp = function(){
        if($scope.token === null){
            alert("A token is required");
            return false;
        }
        $http({
            method: 'POST',
            url: '/cf-ui/Apps/createApp',
            data: 'token='+$scope.token+'&appname='+$scope.appname+'&spaceguid='+$scope.spaceguid,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            $scope.apps = res.data.resources;
            $scope.getApps();
        });
    }

    $scope.updateApp = function(){
        if($scope.token === null){
            alert("A token is required");
            return false;
        }
        $http({
            method: 'POST',
            url: '/cf-ui/Apps/updateApp',
            data: 'token=' + $scope.token + '&appguid=' + $scope.appguid + '&appname=' + $scope.appname,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            $scope.getApps();
        });
    }

    $scope.deleteApp = function(){
        if($scope.token === null){
            alert("A token is required");
            return false;
        }
        $http({
            method: 'POST',
            url: '/cf-ui/Apps/deleteApp',
            data: 'token='+$scope.token+'&appguid='+$scope.appguid,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            $scope.getApps();
        });
    }

    $scope.getServices = function(){
        if($scope.token === null){
            alert("A token is required");
            return false;
        }
        $http({
            method: 'POST',
            url: '/cf-ui/Services/listServices',
            data: 'token=' + $scope.token,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            $scope.services = res.data.resources;
        });
    }


    $scope.getInfo();


});