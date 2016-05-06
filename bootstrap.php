<?php
if(!defined('APPLICATION_ENV')) {
	define('APPLICATION_ENV',
		(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
}
if(!defined('APPLICATION_PATH')) {
	define('APPLICATION_PATH',
		realpath(dirname(__FILE__) ) );
}

if(!defined('BASE_URL')) {
	define('BASE_URL', '/cf-ui' );
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include Mapache and GEIQ API
require APPLICATION_PATH . '/framework/mapache/1/Mapache.php';

// Prepare Mapache configuration
$config = Array(
    'mapache' => array(
        'root_path' => APPLICATION_PATH,
        'include' => Array(
            'cf\CloudFoundry' => 'components/cf/CloudFoundry.php',
            'cf\Apps' => 'components/cf/Apps.php',
            'cf\Files' => 'components/cf/Files.php',
            'cf\Spaces' => 'components/cf/Spaces.php',
            'cf\Buildpacks' => 'components/cf/Buildpacks.php',
            'cf\Routes' => 'components/cf/Routes.php',
            'Session' => 'components/Session.php'
        ),
        'views_path' => APPLICATION_PATH . '/public/app/views/'
    )//,
    //'proxy' => 'PITC-Zscaler-Americas-Cincinnati3PR.proxy.corporate.ge.com:80'

);

$GLOBALS['config'] = $config;

function getGlobal($index){
    $attributePath = explode('/',$index);
    $configTree = $GLOBALS['config'];
    foreach ($attributePath as $attrIndex){
        $configTree = (isset($configTree[$attrIndex]))?
            $configTree[$attrIndex] : null;
        if($configTree == null){break;}
    }

    return $configTree;
}




$handler = new Mapache\Handler($config['mapache']);
