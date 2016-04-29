<?php

/**
 * Created by Fernando Espinosa (fernando.espinosa@ge.com).
 * SSO: 212425641
 */
use Mapache\Controller;

class Spaces extends Controller
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

    function listSpaces(){

        $token = filter_input(INPUT_POST, 'token');

        $params = Array();

        $cfApps = new cf\Spaces($this->endPoint, 2, $token);

        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($cfApps->listSpaces($params));
        $response->display();
    }

    function createApp(){

        $token = filter_input(INPUT_POST, 'token');

        $params = Array(
            //'q' => 'name:geiq-php-test'
        );

        $cfApps = new cf\Spaces($this->endPoint, 2, $token);

        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($cfApps->createApp($params));
        $response->display();
    }


    function getToken(){
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');

        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData(\cf\CloudFoundry::getToken("$username", "$password", "$this->loginEndPoint/oauth/token"));
        $response->display();
    }

}
