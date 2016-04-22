<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Session{
    
    public function __construct() {
        if(!session_start()){
            throw new Exception("Could not start Session");
        }
    }


    public function login($username, $password, $endpoint, $SSHVerificationEnabled = true){
        
        
        
        // Verify login data
        if(isset($_SESSION['cf_access'])){
            // Kill session
            session_unset();
            //if($this->isExpired()){$this->refreshToken();}
            //print_r($_SESSION['cf_access']);
            //return true;
        }
        
        $authEndpoint = \cf\CloudFoundry::getAuthEndpoint($endpoint);
        
        // Get token
        $tokenData = \cf\CloudFoundry::getToken($username, $password, $authEndpoint);
        
        // Dump data into session
        $this->dumpAccessData($tokenData);
        $_SESSION['cf_access']['authorization_endpoint'] = $authEndpoint;
        
        return true;
    }
    
    private function dumpAccessData($accessData){
        
        $_SESSION['cf_access'] = Array();
        
        // Check for errors
        if(isset($accessData->error)){
            throw new Exception("Error: $accessData->error, $accessData->error_description");
        }
        
        // verify token
        // Check for errors
        if(!isset($accessData->access_token)){
            throw new Exception("Error: Access token is missing, cannot start session in cf");
        }
        
        // Fill attributes
        foreach($accessData as $attribute => $value){
            $_SESSION['cf_access'][$attribute] = $value;
        }
        
        $_SESSION['cf_access']['expiration_time'] = time() + $_SESSION['cf_access']['expires_in'];
        
        return true;
    }
    
    public function refreshToken(){
        
        if(!isset($_SESSION['cf_access'], 
                $_SESSION['cf_access']['authorization_endpoint'],
                $_SESSION['cf_access']['refresh_token'])){
            return false;
        }
        
        $authEndpoint = $_SESSION['cf_access']['authorization_endpoint'];
        $refreshToken = $_SESSION['cf_access']['refresh_token'];
        $tokenData = cf\CloudFoundry::refreshToken($refresh_token, $authEndpoint);
        
        $this->dumpAccessData($accessData);
        return true;
    }
    
    public function isExpired(){
        
        if( !isset($_SESSION['cf_access'], $_SESSION['cf_access']['expiration_time']) ){
            return true;
        }
        
        $currentTime = time();
        if(isset($_SESSION['cf_access']) 
                && ($currentTime > $_SESSION['cf_access']['expiration_time'])){
            return true;
        }
        
        
        return false;
    }
    
    public function getToken(){
        
        // Refresh token
        if($this->isExpired()){
            $this->refreshToken();
        }
        
        return (isset($_SESSION['cf_access']['access_token']))? $_SESSION['cf_access']['access_token'] : null;
    }
    
    public function isValid(){
        if(isset($_SESSION['cf_access'])){
            return true;
        }
        
        return false;
    }
    
    public function isActive(){
        
        if(!$this->isValid()){return false;}
        
        // Verify session 
        if( $this->isExpired() ){
            // Try to resume the session
            if( !$this->refreshToken() ){
                return false;
            }
        }

        
        return true;
    }
    
    
    
}