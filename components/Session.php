<?php

/**
 * Cloud foundry GUI Session manager
 * @author: Víctor Hugo garcía Harzbecher <victorhugo.garcia@ge.com >
 * @version: 1.0
 * @since: April 2016
 */
class Session{
    
    /**
     * Session Error message
     * @var String
     */
    private $errorMessage = "";
    
    /**
     * Constructor
     * @throws Exception
     */
    public function __construct() {
        
        // Start Session
        if(!session_start()){
            $this->errorMessage = "Could not start Session";
            return false;
        }
    }

    /**
     * Returns the error message thrown by any of the object functions
     * @return String Error message
     */
    public function getErrorMessage(){
        return $this->errorMessage;
    }

    /**
     * Starts a Cloud Foundry session.
     * @param String $username Cloud foundry username or email
     * @param String $password Account password
     * @param String $endpoint Cloud foundry API endpoint
     * @param String $SSHVerificationEnabled Enable or disable SSL Verification 
     * @return boolean true if session could be stablished, false otherwise
     */
    public function login($username, $password, $endpoint, $SSHVerificationEnabled = true){
        
        // Verify login data
        if(isset($_SESSION['cf_access'])){
            // Kill previous sessions
            session_unset();
        }
        
        // Retrieve API authorization endpoint
        $authEndpoint = \cf\CloudFoundry::getAuthEndpoint($endpoint);
        
        // Try to retrieve token
        $tokenData = \cf\CloudFoundry::getToken($username, $password, $authEndpoint);
        
        // Dump data into session
        if(!$this->dumpAccessData($tokenData)){
            return false;
        }
        
        $_SESSION['cf_access']['authorization_endpoint'] = $authEndpoint;
        $_SESSION['cf_access']['api_endpoint'] = $endpoint;
        
        return true;
    }
    
    /**
     * Dump access Array into session variables
     * @param stdObject $accessData An object containing access data
     * @return boolean true if access data could be retrieved, false if an error ocurr.
     */
    private function dumpAccessData($accessData){
        
        // Clean session data
        unset($_SESSION['cf_access']);
        $_SESSION['cf_access'] = Array();
        
        // Check for errors
        if(isset($accessData->error)){
            $this->errorMessage = "Error: $accessData->error, $accessData->error_description";
            return false;
        }
        
        // verify token
        if(!isset($accessData->access_token)){
            $this->errorMessage = "Error: Access token is missing, cannot start session in cf";
        }
        
        // Fill attributes
        foreach($accessData as $attribute => $value){
            $_SESSION['cf_access'][$attribute] = $value;
        }
        
        $_SESSION['cf_access']['expiration_time'] = time() + $_SESSION['cf_access']['expires_in'];
        
        return true;
    }
    
    /**
     * Verify if Session is valid
     * @return boolean true if session is active, false otherwise
     */
    public function isValid(){
        
        // Verify Session data
        if(!isset($_SESSION['cf_access'])){
            $this->errorMessage = 'Session not started';
            return false;
        }
        
        if(!$this->isActive()){
            return false;
        }
        
        return true;
    }
    
    /**
     * Verify if session is still active
     * @return boolean
     */
    public function isActive(){
        // Verify Session timeout data
        if( !isset($_SESSION['cf_access'], $_SESSION['cf_access']['expiration_time']) ){
            $this->errorMessage = 'Invalid session timeout data';
            return false;
        }
        
        // Verify Session expiration time
        $currentTime = time();
        if($currentTime > $_SESSION['cf_access']['expiration_time']){
            // try to refresh token
            if(!$this->refreshToken()){
                $this->errorMessage = 'Session expired';
                return false;
            }
        }
        
        return true;
    }


    /**
     * Refresh access_token
     * @return boolean returns true if token could be refreshed, false Otherwise
     */
    public function refreshToken(){
        
        if(!isset($_SESSION['cf_access'], 
                $_SESSION['cf_access']['authorization_endpoint'],
                $_SESSION['cf_access']['refresh_token'])){
            $this->errorMessage = "Invalid refresh data";
            return false;
        }
        
        // Refresh token
        $authEndpoint = $_SESSION['cf_access']['authorization_endpoint'];
        $refreshToken = $_SESSION['cf_access']['refresh_token'];
        $tokenData = cf\CloudFoundry::refreshToken($refresh_token, $authEndpoint);
        
        if(!$this->dumpAccessData($tokenData)){
            return false;
        }
        
        return true;
    }
    
    
    /**
     * Retrieves session access token
     * @return String/boolean, Access token if found, false otherwise
     */
    public function getToken(){
        
        // Refresh token
        if( !($this->isActive()) && !($this->refreshToken()) ){
            return false;
        }
        
        // Verify access token
        if( !isset($_SESSION['cf_access']['access_token']) ){
            $this->errorMessage = "Invalid access token";
            return false;
        }
        
        return $_SESSION['cf_access']['access_token'];
    }
    
    /**
     * Retrieves session endpoint
     * @return String/boolean, endpoint url if found, false otherwise
     */
    public function getEndPoint(){
        
        // Verify end point
        if( !isset($_SESSION['cf_access']['api_endpoint']) ){
            $this->errorMessage = "Invalid endpoint";
            return false;
        }
        
        return $_SESSION['cf_access']['api_endpoint'];
    }
    
    
    
}
