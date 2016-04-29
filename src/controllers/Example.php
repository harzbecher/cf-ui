<?php

/**
 * Created by PhpStorm.
 * User: 204072257
 * Date: 4/12/2016
 * Time: 3:27 PM
 */
use Mapache\Controller;

class Example extends Controller
{
    private $endPoint = 'https://api.system.aws-usw02-pr.ice.predix.io';
    private $loginEndPoint = 'https://login.system.aws-usw02-pr.ice.predix.io';

    public function __construct(){
        parent::__construct();
    }

    function indexAction(){
        $this->view->render('Example_view');
    }

    function info(){
        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData(\cf\CloudFoundry::info($this->endPoint));
        $response->display();
    }

    function getToken(){
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');
        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData(\cf\CloudFoundry::getToken("$username", "$password", "$this->loginEndPoint/oauth/token"));
        $response->display();
    }

