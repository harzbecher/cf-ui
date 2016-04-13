<?php
namespace Mapache;

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'config.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'adodb5'.DIRECTORY_SEPARATOR.'adodb.inc.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'adodb5'.DIRECTORY_SEPARATOR.'adodb-active-record.inc.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Model.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'View.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Controller.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'Exception.php';



class Handler {
    
    private $working_url = null;
    
    
    
    private static $currentModule;
    private static $currentRoute;
    
    function __construct($config = null) {
        
        
        // Set global configuration for Mapache Framework
        
        global $mapacheConfig;
        
        if(isset($config) && is_array($config)){
            foreach ($config as $attribute => $value){
                if(isset($mapacheConfig[$attribute])){
                    $mapacheConfig[$attribute] = $value;
                }
            }
        }
        
        $mapacheConfig['controllers_path'] = 
                $mapacheConfig['root_path'].'/'.
                $mapacheConfig['source_folder'].'/'.
                $mapacheConfig['controllers_folder'].'/';
        
        $mapacheConfig['models_path'] = 
                $mapacheConfig['root_path'].'/'.
                $mapacheConfig['source_folder'].'/'.
                $mapacheConfig['models_folder'].'/';
        
        $mapacheConfig['views_path'] = 
                $mapacheConfig['root_path'].'/'.
                $mapacheConfig['source_folder'].'/'.
                $mapacheConfig['views_folder'].'/';
        
        if(isset($mapacheConfig['db'])){
            $connectionString = $mapacheConfig['db']['driver'].'://'.
                $mapacheConfig['db']['user'].':'.
                $mapacheConfig['db']['password'].'@'.
                $mapacheConfig['db']['host'].'/'.
                $mapacheConfig['db']['schema'];
        
            $db = @\NewADOConnection($connectionString);
            @\ADOdb_Active_Record::SetDatabaseAdapter($db);
        }
        
        
        // Explode URL in tokens /Controller/method/argument
        $url_get = filter_input(INPUT_GET, 'url');
        $this->working_url = (isset($url_get)) ? $url_get : null;
        $url_tokens = ($this->working_url != null)? explode('/', rtrim($this->working_url, '/')) : null;
        
        // Controller definition & execution -------------------------------
        $controllerName = (isset($url_tokens[0]))? $url_tokens[0] :  $mapacheConfig['default_controller'];
        $controllerFile = $mapacheConfig['controllers_path'] . $controllerName . '.php';

        if(!file_exists($controllerFile)){throw new Exception("Controller $controllerName does not exists, ($controllerFile)");}
        require $controllerFile;
        if(!is_subclass_of($controllerName, '\Mapache\Controller')){throw new Exception("Controller $controllerName does not extends a valid controller, ($controllerFile)");}
        
        // Load Controller    
        $controller = new $controllerName;
        $controller->setName($controllerName);

        // Load default model for this controller if exists
        if(file_exists($mapacheConfig['models_path'].$controllerName.$mapacheConfig['model_subfix'].'.php')){
            $controller->loadModel($controllerName.$mapacheConfig['model_subfix']);
        }

        // Load Method
        $methodName = ( isset($url_tokens[1]) )? $url_tokens[1] : $mapacheConfig['default_method'];
        
        if (method_exists($controller, $methodName))// Search for method name
        {
            // Search for message
            if (isset($url_tokens[2])) {$controller->{$methodName}($url_tokens[2]);}
            else {$controller->{$methodName}();}
        } else {throw new Exception("Method '$methodName' for Controller '$controllerName' does not exists, ($controllerFile)");}
        
    }
    
    public static function getConfigurationData(){
        global $mapacheConfig;
        return $mapacheConfig;
    }

    
    public static function getCurrentModule()
    {
        return (isset(self::$currentModule))?self::$currentModule:"default";
    }
    
    public static function getCurrentRoute()
    {
        self::$currentRoute;
    }
}
?>