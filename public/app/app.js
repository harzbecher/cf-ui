var cfGui = angular.module("cf-gui", ['flow']);

cfGui.value('config', {
    'host': '/cf-ui/',
    'controllers': {
        'login': "host"+'login'
    }
});

cfGui.factory('routeBuilder', ['config', function(config){
   return  {
       'getController': function(controllerName){
           return config.host+controllerName;
       }
   }
}]);

cfGui.factory('Shared', function(){
    var Shared = {
        activeSpace: null,
        activeDomain: '2a4227ca-1fd5-4c19-a284-29ab47650c8b' //Hardcoded
    }
    
    return Shared;
})