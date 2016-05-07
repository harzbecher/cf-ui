<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace cf;
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'cf_curl.php';

/**
 * Description of Files
 *
 * @author 204072257
 */
class Files {
    private $endpoint = null;
    private $token = null;
    private $apiUrl = null;

    public function __construct($endpoint, $version, $token){
        $this->endpoint = $endpoint;
        $this->token = $token;
        $this->apiUrl = $endpoint."/v$version/apps";

    }
    
    public function ls($appGui, $instanceIndex, $path){
        
        if(!isset($appGui, $instanceIndex, $path)){
            throw new \Exception("Invalid data provided");
        }
        
        $url = $this->apiUrl."/$appGui/instances/$instanceIndex/files/$path";
        
        $http = new cf_curl($url);
        $http->setMethod(cf_curl::$METHOD_GET);
        $http->appendHeaders("Authorization: bearer $this->token");
        $http->setParseMode(cf_curl::$PMODE_RAW);
        
        $files = $http->execute();
        
        $files = preg_split('/\r\n|\r|\n/', $files);
        
        
        foreach ($files as $index => $file){
            // Clear spaces
            $file = preg_replace('/\s+|\t+/', '$', $file);
            // Split
            $elements = explode('$', $file);
            
            $size = (isset($elements[1])) ? $elements[1] : null;
            
            $files[$index] = Array(
                'name' => $elements[0],
                'size' => $size,
                'path' => $path,
                'files'=> 'NULL',
                'subdir'=> 'NULL'
            );
        }
        
        return $files;
    }
    
}
