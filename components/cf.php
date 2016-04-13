<?php
/**
 * Created by PhpStorm.
 * User: 204072257
 * Date: 4/13/2016
 * Time: 3:19 PM
 */


class cf{

    private $apiEndpoint = null;
    private $authEndpoint = null;

    private $profile_settings_path = 'data/profile_data';
    private $profile_settings = null;

    private $proxy = null;

    public function __construct($username){
        // Load user profile
        $this->loadProfile($username);
    }

    public function getProfileSettings(){
        return $this->profile_settings;
    }

    public function info(){
        $curlOpt = Array(
            CURLOPT_URL => $this->profile_settings->endpoint->api_url.'/info'
        );
        $data = $this->cf_curl($curlOpt);
        return $data;
    }

    private function loadProfile($username){
        $settings = getCfgArray();
        $profileFilePath = $settings['root_path'].DIRECTORY_SEPARATOR.$this->profile_settings_path.DIRECTORY_SEPARATOR.$username.'.json';
        $jsonContent = file_get_contents($profileFilePath);
        $this->profile_settings = json_decode($jsonContent);
        if(!isset($this->profile_settings->endpoint->authorization_endpoint)){
            $apiInfo = $this->info();
            $this->profile_settings->endpoint->authorization_endpoint = $apiInfo->authorization_endpoint;
            $this->saveProfile($username);
        }
    }

    public function getToken($username, $password){
        $post = Array(
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password
        );

        $curlOpt = Array(
            CURLOPT_URL => $this->profile_settings->endpoint->authorization_endpoint.'/oauth/token',
            CURLOPT_POST => count($post),
            CURLOPT_POSTFIELDS => "grant_type=password&username=victorhugo.garcia@ge.com&password=CHang3m3"//http_build_query($post)
        );

        $data = $this->cf_curl($curlOpt);
        return $data;

    }

    private function saveProfile($username){
        $fileContent = json_encode($this->profile_settings);
        $settings = getCfgArray();
        $profileFilePath = $settings['root_path'].DIRECTORY_SEPARATOR.$this->profile_settings_path.DIRECTORY_SEPARATOR.$username.'.json';

        $profileFile = fopen($profileFilePath, 'w+');
        fwrite($profileFile, $fileContent);
        fclose($profileFile);
    }


    public function indexAction(){
        print_r($this->profile_settings);
    }

    private function urlfy($paramArray){
        $urlString = "";
        foreach ($paramArray as $key => $value){
            $urlString .= $key.'='.$value.'&';
        }
        return rtrim($urlString, '&');
    }

    private function cf_curl($configArray){

        // Default configuration
        $config = Array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => Array(
                'Content-Type' => 'application/x-www-form-urlencoded;charset=utf-8',
                'Accept' => 'application/json;charset=utf-8',
                'Authorization' => 'Basic Y2Y6'
            )

        );

        // Set Proxy
        if(isset($this->profile_settings->proxy)){
            $config[CURLOPT_PROXY] = $this->profile_settings->proxy;
        }

        // Overwrite configuration
        foreach ($configArray as $opt => $value){
            $config[$opt] = $value;
        }

        /*Transform POST parameters if are array elements
        if( isset($config[CURLOPT_POSTFIELDS])
            && is_array($config[CURLOPT_POSTFIELDS])){
            $config[CURLOPT_POSTFIELDS] = $this->urlfy($config[CURLOPT_POSTFIELDS]);
        }*/

        print_r($config);

        $handler = curl_init();
        curl_setopt_array($handler, $config);

        // Execute request and get response
        if (!($response = curl_exec($handler))) {
            throw new Exception(curl_error($handler));}


        curl_close($handler);

        $response = json_decode($response);
        return $response;
    }
}