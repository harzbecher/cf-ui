
cfGui.controller('apps', ['$scope', '$http', 'routeBuilder', 'Shared', function($scope, $http, routeBuilder, Shared){
    
    $scope.errorMessage = "";
    $scope.loading = false;
    
    $scope.loadingApps = false;
    $scope.lockApps = false;
    
    $scope.loadingSettings = false;
    $scope.lockSettings = false;
    
    $scope.selectedApp = null;
    
    $scope.template = 'apps.html';
    
    $scope.apps = null;
    
    // Application sections
    $scope.appSections = [
        'Summary',
        'Files',
        'Config',
        'Services',
        'Push'
    ];
    
    $scope.defaultSection = 'Summary';
    
    $scope.activeSection = null;
    
    /**
     * Application model
     */
    $scope.applicationModel = {
        "attributes": {
            "name": {
                "name": "name",
                "type": "text",
                "required": true,
                "value": null,
                "visible": true,
                "placeholder": "Application Name"
            },
            "space_guid": {
                "name": "space_guid",
                "type": "text",
                "required": true,
                "value": null,
                "visible": false,
                "placeholder": null
            },
            "instances": {
                "name": "instances",
                "type": "number",
                "required": true,
                "value": null,
                "visible": true,
                "placeholder": "Number of instances"
            },
            "buildpack": {
                "name": "buildpack",
                "type": "buildpack",
                "required": false,
                "value": null,
                "visible": true,
                "placeholder": null
            },
            "state": {
                "name": "state",
                "type": "state",
                "required": false,
                "value": null,
                "visible": true,
                "placeholder": "Initial state"
            },
            "command": {
                "name": "command",
                "type": "text",
                "required": false,
                "value": null,
                "visible": true,
                "placeholder": "Application Command"
            },
            "disk_quota": {
                "name": "disk_quota",
                "type": "number",
                "required": false,
                "value": null,
                "visible": true,
                "placeholder": "Disk size"
            },
            "memory": {
                "name": "memory",
                "type": "number",
                "required": false,
                "value": null,
                "visible": true,
                "placeholder": "Available memory"
            },
            "ports": {
                "name": "ports",
                "type": "text",
                "required": false,
                "value": null,
                "visible": true,
                "placeholder": "Application port"
            }
        }
    }
    
    
    /**
     * Creates an application using application model
     * @returns {undefined}
     */
    $scope.create = function(){
        // Set active space
        $scope.applicationModel.attributes.space_guid.value = Shared.activeSpace;
        // Set default instance value
        $scope.applicationModel.attributes.instances.value = 
                ($scope.applicationModel.attributes.instances.value !== null)? 
                    $scope.applicationModel.attributes.instances.value : 1;
        
        // Validate data
        if($scope.applicationModel.attributes.name === null ||
                $scope.applicationModel.attributes.space === null ||
                $scope.applicationModel.attributes.instances === null){
            $scope.throwError("Missing required data");
        }
        
        // Build URL
        var controllerPath = routeBuilder.getController('apps')+'/add';
        
        
        $http.post(controllerPath, JSON.stringify($scope.applicationModel))
            .success(function (res) {
                
                // Verify and throw errors
                if(res.status === 'error'){
                    $scope.throwError(res.data);
                    return;
                }

                if(res.data.error_code !== undefined){
                    $scope.throwError(res.data.description);
                    return;
                }
                
                $scope.apps.push(res.data);
                $scope.closeAddAppModal();
            });
    }
    
    
    
    
    /**
     * Loads defined application section
     * @param {type} section
     * @returns {Boolean}
     */
    $scope.loadSection = function(section){
        
        if($scope.selectedApp === undefined || $scope.selectedApp === null){
            return true;
        }
        
        // Verify section
        if(section === undefined || section === null){
            section = $scope.defaultSection;
        }
        
        $scope.activeSection = section;
        
        switch($scope.activeSection){
            case 'Summary':
                $scope.getEnvForSelectedApp();
                break;
            case 'Files':
                $scope.updateFoldersForSelectedApp($scope.selectedApp);
                break;
        }
    }
    
    $scope.getTargetForUpload = function(){
        return routeBuilder.getController('UploadApps') + '/UploadAction/' + $scope.selectedApp.metadata.guid + '/';
    }
    
    $scope.fileUploadError = function( $file, $message, $flow ){
        console.log($file);
        console.log($message);
    }
    
    $scope.fileUploadComplete = function( $flow ){
        $http.post(routeBuilder.getController('UploadApps') + '/CompleteUploadAction/' + $scope.selectedApp.metadata.guid + '/')
            .success(function (response) {
                
                // Verify and throw errors
                if(response.status === 'error'){
                    $scope.throwError(response.data);
                    return;
                }

                if(response.data.error_code !== undefined){
                    $scope.throwError(res.data.description);
                    return;
                }
            });
    }

    
    
    /**
     * Retrieves an application list
     * @returns {undefined}
     */
    $scope.getAppsList = function(){
        
        $scope.loadingApps = true;
        
        
        var controllerPath = routeBuilder.getController('apps')+'/listApps';
        
        $http({
            method: 'GET',
            url: controllerPath+'/'+Shared.activeSpace ,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            // Verify and throw errors
            if(res.status === 'error'){
                $scope.throwError(res.data);
            }
            
            
            $scope.apps = res.data.resources;
            
            // Stop loader
            $scope.loadingApps = false;
            $scope.lockApps = false;
        });
        
        
        return;
    };
    
    /**
     * Get env for selected application
     * @returns true
     */
    $scope.getEnvForSelectedApp = function (){
        
        if($scope.selectedApp.env !== undefined){
            return true;
        }
        
        $scope.lockApps = true;
        $scope.lockSettings = true;
        $scope.loadingSettings = true;
        
        var controllerPath = routeBuilder.getController('apps')+'/getEnv/'+$scope.selectedApp.metadata.guid;
        
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
            
            // Dump data into selected app
            $scope.selectedApp.env = {
                'uris' : env.application_env_json.VCAP_APPLICATION.uris,
                'limits' : env.application_env_json.VCAP_APPLICATION.limits,
                'space' : {
                    'id': env.application_env_json.VCAP_APPLICATION.space_id,
                    'name' : env.application_env_json.VCAP_APPLICATION.space_name
                },
                'users'  : env.application_env_json.VCAP_APPLICATION.users 
            };
            
            // Unlock Apps
            $scope.lockApps = false;
            $scope.lockSettings = false;
            $scope.loadingSettings = false;
        });
        
        return true;
    }
    
    
    /**
     * Returns badge class for defined state
     * @param {String} state
     * @returns {String}
     */
    $scope.getStateClass = function(state){
        switch(state){
            case 'STOPPED':
                return 'label-danger';
            case 'STARTED':
                return 'label-success';
                
        }
    }
    
    $scope.updateFoldersForSelectedApp = function(application){
        
        // Set files object
        if(application.files !== undefined){
            return true;
        }
        
        var guid = application.metadata.guid;
        
        var controllerPath = routeBuilder.getController('apps')+'/listFiles';
        var parameters = "guid="+guid+
                "&instance_index="+0+
                "&path=";
        
        $http({
            method: 'GET',
            url: controllerPath+"?"+parameters,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(res){
            // Verify and throw errors
            if(res.status === 'error'){
                $scope.throwError(res.data);
                return;
            }
            
            if(res.data.error_code !== undefined){
                $scope.throwError(res.data.description);
                return;
            }
            
            application.files = res.data;
        });
        
        
    }
    
    // UI Events
    $scope.selectApp = function (appObject){
        $scope.selectedApp = appObject;
        $scope.loadSection($scope.defaultSection);
    };
    
    // Events
    $scope.$on("spaceChanged", function(event, args){
        $scope.getAppsList();
    });
    
    
    
    $scope.throwError = function($message){
        $scope.errorMessage = $message;
        console.log($message);
    }
    
    // Jquery hybrid functions
    $scope.closeAddAppModal = function(){
        $('#addAppModal').modal('hide');
    }
    
    
}]);