<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login
 *
 * @author 204072257
 */
class login extends Mapache\Controller{
    
    private $session = null;
    
    public function __construct() {
        // Start Session
        $this->session = new Session();
        parent::__construct();
    }
    
    public function indexAction(){
        
        // Verify Session and redirect if it is active
        if($this->session->isActive()){
            header('Location:home');
        }
        
        // Load login view
        $this->view->render('login.html');
    }
    
    public function login(){
        
        // Prepare response
        $response = new \Mapache\Response(\Mapache\Response::$RES_ACTION);
        
        try{
            
            // Gather data
            $username = filter_input(INPUT_POST, 'username');
            $password = filter_input(INPUT_POST, 'password');
            $endpoint = filter_input(INPUT_POST, 'endpoint');
            
            // verify login data
            if(!isset($username, $password, $endpoint)){
                throw new Exception("Missing login data");
            }
            
            // Start Session
            if($this->session->login($username, $password, $endpoint)){
                $response->setStatus(\Mapache\Response::$STAT_OK);
                $response->setData("started");
            }
            
        } catch (Exception $ex) {
            $response->setStatus(\Mapache\Response::$STAT_ERROR);
            $response->setData($ex->getMessage());
        }

        $response->display();
    }
}
