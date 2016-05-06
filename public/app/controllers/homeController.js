
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
            
            // Dump spaces
            $scope.spaces = res.data.resources;
            
            console.log($scope.spaces);
            // Set first element as active space
            $scope.activeSpace = $scope.spaces[0];
            $scope.setActiveSpace();
            //$scope.setActiveSpace();
        });
    };
    
    
    $scope.setActiveSpace = function(){
        Shared.activeSpace = $scope.activeSpace.metadata.guid;
        $scope.$broadcast("spaceChanged", {spaceGuid: Shared.activeSpace});
        //console.log($scope.activeSpace);
        
    }
    
    
    
    $scope.initComponents();
    
}]);