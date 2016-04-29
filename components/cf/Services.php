<?php

namespace cf;
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'cf_curl.php';

/**
 * Cloud foundry apps REST Client
 * @author: Fernando Espinosa <fernando.espinosa@ge.com >
 * @version: 1.0
 * @since: April 2016
 */
class Services {

    private $endpoint = null;
    private $token = null;
    private $apiUrl = null;

    public function __construct($endpoint, $version, $token){
        $this->endpoint = $endpoint;
        $this->token = $token;
        $this->apiUrl = $endpoint."/v$version/services";

    }

    public function listServices($args){

        $http = new cf_curl($this->apiUrl);
        $http->setMethod(cf_curl::$METHOD_GET);
        $http->appendHeaders("Authorization: bearer $this->token");
        $http->setParameters($args);
        return $http->execute();
    }

}
