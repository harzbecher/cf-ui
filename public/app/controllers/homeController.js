
cfGui.controller('home', ['$scope', '$http', 'routeBuilder', 'Shared', function($scope, $http, routeBuilder, Shared){
    
    $scope.activeSpace = null;
    $scope.spaces = null;
    
    $scope.initComponents = function(){
        // Load spaces list
        $scope.loadSpaces();
    };
    
    $scope.loadSpaces = function(){
        
        var controllerPath = routeBuilder.getController('Spaces');
        
        $http({
            method: 'GET',
            url: controllerPath+"/listSpaces",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            // Verify and throw errors
            if(res.status === 'error'){
                $scope.throwError(res.data);
            }
            
            $scope.spaces = res.data.resources;
            // Set default space
            
            $scope.setActiveSpace($scope.spaces[0].metadata.guid);
        });
    };
    
    
    $scope.setActiveSpace = function(spaceGuid){
        $scope.activeSpace = spaceGuid;
        Shared.activeSpace = $scope.activeSpace;
        $scope.$broadcast("spaceChanged", {spaceGuid: $scope.activeSpace});
        
        
        console.log($scope.activeSpace);
        
    }
    
    
    
    $scope.initComponents();
    
}]);