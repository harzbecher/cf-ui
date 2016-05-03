
cfGui.directive('loader', function(){
   return{
       restrict: 'E',
       transclude: true,
       templateUrl: '/cf-ui/public/app/views/templates/loaderDirective.html',
       controller: ['$scope', function($scope){
       }]
   } 
});

