
cfGui.controller('login', ['$scope', '$http', 'routeBuilder', function($scope, $http, routeBuilder){
    
    $scope.username = null;
    $scope.password = null;
    $scope.endpoint = "https://api.system.aws-usw02-pr.ice.predix.io";
    $scope.errorMessage = "";
    $scope.loading = false;
    
    $scope.ignoreSSL = false;
    console.log(routeBuilder.getController('login'));
    
    $scope.login = function(){
        
        $scope.loading = true;
        $scope.errorMessage = "";
        
        var controllerPath = routeBuilder.getController('login')+'/login';
        
        $http({
            method: 'POST',
            url: controllerPath,
            data: 'username='+$scope.username+'&password='+$scope.password+'&endpoint='+$scope.endpoint,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            // Verify and throw errors
            if(res.status === 'error'){
                $scope.throwError(res.data);
            }
            
            if(res.status === 'ok' && res.data === 'started'){
                window.location.replace(routeBuilder.getController('home'));
            }
            // Stop loader
            $scope.loading = false;
        });
        
        
        return;
    }
    
    $scope.checkEnter = function($event){
        var keyCode = $event.which || $event.keyCode;
        if (keyCode === 13 && $scope.username !== null && $scope.password !== null) {
            $scope.login();
        }
    }
    
    $scope.throwError = function($message){
        $scope.errorMessage = $message;
    }
    
}]);