<?php

namespace Mapache;

class Controller {

    private $name;
    public $view;
    public $model;
    
    
    /**
     * Constructor
     */
    public function __construct() 
    {   
        // Generate view for current controller
        $this->view = new View();
    }
    
    
    /**
     * Retrieve model from current module.
     * @param String $modelName
     * @return Model
     * @throws Exception if specified module not exists in the module environment
     */
    public function loadModel($modelName){
        global $mapacheConfig;
        $modelPath = $mapacheConfig['models_path'].$modelName.'.php';
        
        if(file_exists($modelPath))
        {
            require $modelPath;
            if(!is_subclass_of($modelName, '\Mapache\Model')){
                throw new Exception("Model $modelName does not extends a valid model, ($modelPath)");
            }
            return new $modelName;
        }else {
            throw new Exception("Selected model $modelName does not exists, ($modelPath)");
            
        }
    }
    
    public function setName($controllerName){
        $this->name = $controllerName;
    }

}
?>
