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
    }
    
    return Shared;
})