
cfGui.controller('apps', ['$scope', '$http', 'routeBuilder', 'Shared', function($scope, $http, routeBuilder, Shared){
    
    
    console.log(Shared.activeSpace);
    
    $scope.errorMessage = "";
    $scope.loading = false;
    
    $scope.selectedApp = null;
    
    $scope.template = 'apps.html';
    
    $scope.apps = null;
    
    // Application sections
    $scope.appSections = [
        'Summary',
        'Files',
        'Config',
        'Services'
    ];
    
    $scope.defaultSection = 'Summary';
    
    $scope.activeSection = null;
    
    
    
    $scope.selectApp = function (appObject){
        $scope.selectedApp = appObject;
        $scope.loadSection($scope.defaultSection);
    };
    
    $scope.updateFolders = function(application){
        
        // Set files object
        if(application.files !== undefined){
            return true;
        }
        
        var guid = application.metadata.guid;
        
        var controllerPath = routeBuilder.getController('apps')+'/listFiles';
        var parameters = "guid="+guid+
                "&instance_index="+0+
                "&path=app/htdocs";
        
        $http({
            method: 'GET',
            url: controllerPath+"?"+parameters,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            // Verify and throw errors
            if(res.status === 'error'){
                $scope.throwError(res.data);
            }
            
            application.files = res.data;
            console.log(application);
        });
        
        
    }
    
    $scope.loadSection = function(section){
        $scope.activeSection = section;
        switch($scope.activeSection){
            case 'Summary':
                $scope.getEnv($scope.selectedApp.metadata.guid);
                console.log($scope.selectedApp.env);
            case 'Files':
                $scope.updateFolders($scope.selectedApp);
        }
    }
    
    
    $scope.getAppsList = function(){
        
        $scope.loading = true;
        
        var controllerPath = routeBuilder.getController('apps')+'/listApps';
        
        $http({
            method: 'GET',
            url: controllerPath,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            // Verify and throw errors
            if(res.status === 'error'){
                $scope.throwError(res.data);
            }
            
            $scope.apps = res.data.resources;
            console.log($scope.apps);
            
            // Stop loader
            $scope.loading = false;
        });
        
        
        return;
    };
    
    $scope.getEnv = function (guid){
        
        var controllerPath = routeBuilder.getController('apps')+'/getEnv/'+guid;
        
        $http({
            method: 'GET',
            url: controllerPath,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            // Verify and throw errors
            if(res.status === 'error'){
                $scope.throwError(res.data);
            }
            
            var env = res.data;
            
            $scope.selectedApp.env = {
                'uris' : env.application_env_json.VCAP_APPLICATION.uris,
                'limits' : env.application_env_json.VCAP_APPLICATION.limits,
                'space' : {
                    'id': env.application_env_json.VCAP_APPLICATION.space_id,
                    'name' : env.application_env_json.VCAP_APPLICATION.space_name
                },
                'users'  : env.application_env_json.VCAP_APPLICATION.users 
            };
            console.log(env);
            
            // Stop loader
            $scope.loading = false;
        });
    }
    
    $scope.getFileList = function(guid, instanceIndex, path){
        
        
        
        
    };
    
    $scope.getStateClass = function(state){
        switch(state){
            case 'STOPPED':
                return 'label-danger';
            case 'STARTED':
                return 'label-success';
                
        }
        
    }
    
    $scope.getAppsList();
    
    $scope.throwError = function($message){
        $scope.errorMessage = $message;
    }
    
}]);