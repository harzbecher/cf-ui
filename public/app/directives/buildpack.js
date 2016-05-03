
cfGui.directive('buildpack', ['$http', 'routeBuilder', function($http, routeBuilder){
   return{
       restrict: 'E',
       replace: true,
       scope: {
           ngModel: '=',
           class: '='
       },
       controller: function($scope){
            var controllerPath = routeBuilder.getController('buildpacks')+'/listBuildpacks';
               
            $http.get(controllerPath)
                .then(function (result) {
                    $scope.buildpacks = result.data.data.resources;
                    console.log("--");
                    console.log($scope.buildpacks);
                    console.log("--");
                }, function (result) {
                    
                });
       },
       templateUrl: '/cf-ui/public/app/views/templates/buildpackDirective.html',
   } 
}]);

