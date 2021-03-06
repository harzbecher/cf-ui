<?php

namespace cf;
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'cf_curl.php';

/**
 * Cloud foundry apps REST Client
 * @author: Víctor Hugo garcía Harzbecher <victorhugo.garcia@ge.com >
 * @version: 1.0
 * @since: May 2016
 */
class Routes {

    private $endpoint = null;
    private $token = null;
    private $apiUrl = null;

    public function __construct($endpoint, $version, $token){
        $this->endpoint = $endpoint;
        $this->token = $token;
        $this->apiUrl = $endpoint."/v$version/routes";
    }

    public function add($args = null){
        
        $http = new cf_curl($this->apiUrl);
        $http->setMethod(cf_curl::$METHOD_POST);
        $http->appendHeaders("Authorization: bearer $this->token");
        if(isset($args)){
            $http->setParameters($args);
        }
        
        return $http->execute();
    }

}
