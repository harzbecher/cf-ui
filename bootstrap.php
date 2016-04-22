<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include Mapache and GEIQ API
include 'C:/xampp/htdocs/cf-ui/framework/mapache/1/Mapache.php';
//C:\xampp\htdocs\cf-ui\framework\mapache\1

// Prepare Mapache configuration
$config = Array(
    'mapache' => array(
        'root_path' => 'C:/xampp/htdocs/cf-ui',
        'include' => Array(
            'cf\CloudFoundry' => 'components/cf/CloudFoundry.php',
            'cf\Apps' => 'components/cf/Apps.php',
            'Session' => 'components/Session.php'
        ),
        'views_path' => 'C:/xampp/htdocs/cf-ui/public/app/views/'
    ),
    'proxy' => 'iss-americas-pitc-cincinnatiz.proxy.corporate.ge.com:80'

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