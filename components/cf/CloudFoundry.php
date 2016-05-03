<?php

namespace cf;
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'cf_curl.php';

/**
 * Cloud foundry REST Client
 * @author: Víctor Hugo garcía Harzbecher <victorhugo.garcia@ge.com >
 * @version: 1.0
 * @since: April 2016
 */
class CloudFoundry {

    public function __construct(){
    }

    /**
     * Provide oAuth token for Cloud foundry
     * @param $username Cloud foundry username
     * @param $password account password
     * @param $authEndpoint Cloud foundry login end point
     * @return stdClass
     * @throws Exception
     */
    public static function getToken($username, $password, $authEndpoint = null){

        $post = Array(
            'grant_type' => 'password',
            'username' => "$username",
            'password' => "$password"
        );

        // Prepare curl request
        $http = new cf_curl($authEndpoint.'/oauth/token', cf_curl::$METHOD_POST);
        $http->setParameters($post, 'http_query');
        $http->appendHeaders('Authorization: Basic Y2Y6');

        return $http->execute();
    }
    
    public static function refreshToken($refresh_token, $authEndpoint = null){

        $post = Array(
            'grant_type' => 'refresh_token',
            'refresh_token' => "$refresh_token"
        );

        // Prepare curl request
        $http = new cf_curl($authEndpoint, cf_curl::$METHOD_POST);
        $http->setParameters($post, 'http_query');
        $http->appendHeaders('Authorization: Basic Y2Y6');

        return $http->execute();
    }
    
    public static function getAuthEndpoint($endpoint){
        $info = self::info($endpoint);
        if(!isset($info->authorization_endpoint)){
            throw new Exception("Cannot retrieve authorization endpoint, please verify your api url");
        }
        return $info->authorization_endpoint;
    }


    /**
     * Returns Endpoint information
     * @param $endpoint Cloud foundry information
     * @return mixed
     */
    public static function info($endpoint){

        $http = new cf_curl($endpoint.'/info');
        return $http->execute();
    }

}
