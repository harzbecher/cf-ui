<?php

/**
 * Created by PhpStorm.
 * User: 204072257
 * Date: 4/12/2016
 * Time: 3:27 PM
 */
use Mapache\Controller;

class apps extends Controller
{
    private $session = null;

    public function __construct(){
        parent::__construct();
        
        $this->session = new Session();
        if(!$this->session->isActive()){
            echo $this->session->getErrorMessage();
            header('Location:login');
        }
    }
    
    public function IndexAction(){
        $this->view->render('apps.html');
    }
            
    function listApps(){
        
        $response = new \Mapache\Response();
        try {
            $apps = new cf\Apps($this->session->getEndPoint(), 
                '2', 
                $this->session->getToken());
            
            $response->setData($apps->listApps());
            $response->setStatus(\Mapache\Response::$STAT_OK);            
        } catch (Exception $ex) {
            $response->setStatus(\Mapache\Response::$RES_ERROR);
            $response->setData($ex->getMessage());
        }
        
        $response->display();
        
    }
    
    function listFiles(){
        $guid = filter_input(INPUT_GET, 'guid');
        $instance = filter_input(INPUT_GET, 'instance_index');
        $path = filter_input(INPUT_GET, 'path');
        
        $response = new \Mapache\Response();
        try {
            
            if(!isset($guid, $instance, $path)){
                throw new Exception("Missing required parameters");
            }
            
            $files = new cf\Files(
                $this->session->getEndPoint(), 
                '2', 
                $this->session->getToken());
            
            $response->setData($files->ls($guid, $instance, $path));
            $response->setStatus(\Mapache\Response::$STAT_OK);            
        } catch (Exception $ex) {
            $response->setStatus(\Mapache\Response::$RES_ERROR);
            $response->setData($ex->getMessage());
        }
        
        $response->display();
        
    }
    
    function getEnv($guid){
        
        $response = new \Mapache\Response();
        try {
            $apps = new cf\Apps($this->session->getEndPoint(), 
                '2', 
                $this->session->getToken());
            
            $response->setData($apps->getEnv($guid));
            $response->setStatus(\Mapache\Response::$STAT_OK);            
        } catch (Exception $ex) {
            $response->setStatus(\Mapache\Response::$RES_ERROR);
            $response->setData($ex->getMessage());
        }
        
        $response->display();
        
    }

}