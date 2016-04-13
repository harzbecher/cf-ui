<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include Mapache and GEIQ API
include 'C:/xampp/htdocs/cf-ui/framework/mapache/1/Mapache.php';
//C:\xampp\htdocs\cf-ui\framework\mapache\1

// Prepare Mapache configuration
$config = Array(
    'root_path' => 'C:/xampp/htdocs/cf-ui',
    'include' => Array(
        'cf' => 'components/cf.php'
    )
);

function getCfgArray(){
    global $config;
    return $config;
}

$handler = new Mapache\Handler($config);