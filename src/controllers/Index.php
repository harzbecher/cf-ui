<?php

/**
 * Created by PhpStorm.
 * User: 204072257
 * Date: 4/11/2016
 * Time: 8:16 PM
 */

use Mapache\Controller;

class Index extends Controller
{
    public function IndexAction(){
        $this->view->render('Home');
    }
    
    public function getInfo(){
        $url = 'https://api.system.aws-usw02-pr.ice.predix.io/info';
        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $url);
        curl_setopt($handler, CURLOPT_PROXY, 'iss-americas-pitc-cincinnatiz.proxy.corporate.ge.com:80');
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handler, CURLOPT_HEADER, false);
        // Execute request and get response
        if (!($response = curl_exec($handler))) {
            throw new Exception(curl_error($handler));}
        curl_close($handler);

        $response = json_decode($response);
        //if(!isset($response->data)){throw new Exception("Malformed response for request '$url'");}
        print_r($response);
    }
}