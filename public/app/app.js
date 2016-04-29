var cfGui = angular.module("cf-gui", []);

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