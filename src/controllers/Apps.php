<?php

/**
 * Created by PhpStorm.
 * User: 204072257
 * Date: 4/12/2016
 * Time: 3:27 PM
 */
use Mapache\Controller;

class Apps extends Controller
{
    private $endPoint = 'https://api.system.aws-usw02-pr.ice.predix.io';
    private $loginEndPoint = 'https://login.system.aws-usw02-pr.ice.predix.io';

    public function __construct(){
        parent::__construct();
    }

    function indexAction(){
        //$this->view->render('Example_view');
    }

    function listApps(){

        $token = filter_input(INPUT_POST, 'token');
        $spaceguid = filter_input(INPUT_POST, 'spaceguid');

        $params = Array(
            'q' => 'space_guid:' . $spaceguid
        );

        $cfApps = new cf\Apps($this->endPoint, 2, $token);

        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($cfApps->listApps($params));
        $response->display();
    }

    function createApp(){


        $token = filter_input(INPUT_POST, 'token');
        $name = filter_input(INPUT_POST, 'appname');
        $space_guid = filter_input(INPUT_POST, 'spaceguid');

        $params = Array(
            "name" => $name,
            "space_guid" => $space_guid
        );

        $cfApps = new cf\Apps($this->endPoint, 2, $token);

        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($cfApps->createApp($params));
        $response->display();
    }

    function deleteApp(){
        $token = filter_input(INPUT_POST, 'token');
        $appguid = filter_input(INPUT_POST, 'appguid');

        $params = Array(
        );

        $cfApps = new cf\Apps($this->endPoint, 2, $token);

        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($cfApps->deleteApp($params, $appguid));
        $response->display();
    }

    function updateApp(){
        $token = filter_input(INPUT_POST, 'token');
        $appguid = filter_input(INPUT_POST, 'appguid');
        $name = filter_input(INPUT_POST, 'appname');

        $params = Array(
            "name" => $name
        );

        $cfApps = new cf\Apps($this->endPoint, 2, $token);

        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($cfApps->updateApp($params, $appguid));
        $response->display();
    }

}
