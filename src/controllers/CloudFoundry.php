<?php

/**
 * Created by PhpStorm.
 * User: 204072257
 * Date: 4/12/2016
 * Time: 3:27 PM
 */
use Mapache\Controller;

class CloudFoundry extends Controller
{

    public function __construct(){
        parent::__construct();
    }

    function indexAction(){
        $cf = new cf('victorhugo.garcia@ge.com');
        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($cf->info());
        $response->display();
    }

    function mySettings(){
        $cf = new cf('victorhugo.garcia@ge.com');
        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($cf->getProfileSettings());
        $response->display();
    }

    function getToken(){
        $cf = new cf('victorhugo.garcia@ge.com');
        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($cf->getToken('victorhugo.garcia@ge.com', 'CHang3m3'));
        $response->display();
    }

}