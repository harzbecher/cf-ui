<?php

namespace cf;
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'cf_curl.php';

set_time_limit(0);

/**
 * Cloud foundry apps REST Client
 * @author: Víctor Hugo garcía Harzbecher <victorhugo.garcia@ge.com >
 * @author: Fernando Espinosa <fernando.espinosa@ge.com>
 * @version: 1.0
 * @since: April 2016
 */
class Apps {

    private $endpoint = null;
    private $token = null;
    private $apiUrl = null;

    public function __construct($endpoint, $version, $token){
        $this->endpoint = $endpoint;
        $this->token = $token;
        $this->apiUrl = $endpoint."/v$version/apps";
    }

    public function listApps($args=null){

        $http = new cf_curl($this->apiUrl);
        $http->setMethod(cf_curl::$METHOD_GET);
        $http->appendHeaders("Authorization: bearer $this->token");
        if(isset($args)){
            $http->setParameters($args);
        }

        return $http->execute();
    }



    public function getEnv($appguid){

        if(!isset($appguid)){
            throw new Exception('Invalid guid provided');
        }

        $http = new cf_curl($this->apiUrl."/$appguid/env");
        $http->setMethod(cf_curl::$METHOD_GET);
        $http->appendHeaders("Authorization: bearer $this->token");

        return $http->execute();
    }

    public function createApp($args){

        $http = new cf_curl($this->apiUrl);
        $http->setMethod(cf_curl::$METHOD_POST);
        $http->appendHeaders("Authorization: bearer $this->token");
        $http->setParameters($args);
        return $http->execute();
    }

    public function updateApp($args, $appguid){

        $http = new cf_curl($this->apiUrl . '/' . $appguid);
        $http->setMethod(cf_curl::$METHOD_PUT);
        $http->appendHeaders("Authorization: bearer $this->token");
        $http->setParameters($args);
        return $http->execute();
    }

    public function deleteApp($args, $appguid){

        $http = new cf_curl($this->apiUrl . '/' . $appguid);

        $http->setMethod(cf_curl::$METHOD_DELETE);
        $http->appendHeaders("Authorization: bearer $this->token");
        $http->setParameters($args);
        return $http->execute();
    }
    
    public function addRoute($routeguid, $appguid){

        $url = $this->apiUrl."/$appguid/routes/$routeguid";
        $http = new cf_curl($url);
        $http->setMethod(cf_curl::$METHOD_PUT);
        $http->appendHeaders("Authorization: bearer $this->token");
        return $http->execute();
    }
    
    

    public function restageApp($args, $appguid){
        $http = new cf_curl($this->apiUrl . '/' . $appguid . '/restage');
        $http->setMethod(cf_curl::$METHOD_POST);
        $http->appendHeaders("Authorization: bearer $this->token");
        $http->setParameters($args);
        return $http->execute();
    }


}
