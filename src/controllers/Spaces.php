<?php


/**
 * Cloud foundry http Service
 * @author: Fernando Espinosa <fernando.espinosa@ge.com>
 * @version: 1.0
 * @since: April 2016
 */
use Mapache\Controller;

class Spaces extends Controller
{
    private $session = null;

    /**
     * Constructor
     * @author Victor Hugo Garcia Harzbecher <victorhugo.garcia@ge.com>
     */
    public function __construct(){
        parent::__construct();
        $this->session = new Session();
        if(!$this->session->isActive()){
            echo $this->session->getErrorMessage();
            header('Location:login');
        }
    }

    /**
     * List available spaces for defined user
     */
    function listSpaces(){

        $params = Array();

        // Prepare Space object
        $cfApps = new cf\Spaces(
                $this->session->getEndPoint(), 
                2, 
                $this->session->getToken());

        // Get Response
        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($cfApps->listSpaces($params));
        $response->display();
    }

    function createApp(){

        $params = Array(
            //'q' => 'name:geiq-php-test'
        );

        // Prepare Space object
        $cfApps = new cf\Spaces(
                $this->session->getEndPoint(), 
                2, 
                $this->session->getToken());

        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($cfApps->createApp($params));
        $response->display();
    }

}
