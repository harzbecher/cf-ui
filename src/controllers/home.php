<?php

/**
 * Created by PhpStorm.
 * User: 204072257
 * Date: 4/12/2016
 * Time: 3:27 PM
 */
use Mapache\Controller;

class home extends Controller
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

    function indexAction(){
        $this->view->render('home.html');
        //echo "Hey you did it!, here is your token: ".$this->session->getToken();
        
    }

}