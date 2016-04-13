<?php

namespace Mapache;

/**
 * Global Mapache configuration1
 */
$mapacheConfig = Array(
    'root_path' => '/var/www/html/',
    'source_folder' => 'src',
    'controllers_folder' => 'controllers',
    'models_folder' => 'models',
    'views_folder' => 'views',
    'default_controller' => 'Index',
    'default_method' => 'indexAction',
    'components_folder' => 'components',
    'libs_folder' => 'lib',
    'controllers_path' => null,
    'models_path' => null,
    'views_path' => null,
    'model_subfix' => '_model',
    'lib' => Array(
        'ADONewConnection' => 'lib/adodb5/adodb.inc.php'
    ),
    'components' => Array(
        'Mapache\Response' => 'Response.php',
        'Mapache\Logger' => 'Logger.php',
        'Mapache\Connection' => 'Connection/Connection.php',
    ),
    'include' => Array()
);

spl_autoload_register(function($className){
    
    global $mapacheConfig;
    
    if(isset($mapacheConfig['components'][$className])){
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.
                $mapacheConfig['source_folder'].DIRECTORY_SEPARATOR.
                $mapacheConfig['components_folder'].DIRECTORY_SEPARATOR.
                $mapacheConfig['components'][$className];
        return true;
    }
    
    if(isset($mapacheConfig['include'][$className])){
        require_once $mapacheConfig['root_path'].DIRECTORY_SEPARATOR.
                $mapacheConfig['include'][$className];
        return true;
    }
    
    if(isset($mapacheConfig['lib'][$className])){
        require_once dirname(__FILE__).DIRECTORY_SEPARATOR.
                $mapacheConfig['source_folder'].DIRECTORY_SEPARATOR.
                $mapacheConfig['libs_folder'].DIRECTORY_SEPARATOR.
                $mapacheConfig['lib'][$className];
        return true;
    }
    
});