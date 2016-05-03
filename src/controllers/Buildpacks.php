<?php

/**
 * Created by PhpStorm.
 * User: 204072257
 * Date: 4/12/2016
 * Time: 3:27 PM
 */
use Mapache\Controller;
use Mapache\Response;


/**
 * Apps REST Service
 * @author: Víctor Hugo garcía Harzbecher <victorhugo.garcia@ge.com >
 * @author: Fernando Espinosa <fernando.espinosa@ge.com>
 * @version: 1.0
 * @since: April 2016
 */
class Buildpacks extends Controller
{
    
    private $session = null;
    private $buildpacks = null;

    public function __construct(){
        parent::__construct();
        $this->session = new Session();
        if(!$this->session->isActive()){
            echo $this->session->getErrorMessage();
            header('Location:login');
        }
        
        // Initialize Apps API Client
        $this->buildpacks = new cf\Buildpacks(
            $this->session->getEndPoint(), 
            2, 
            $this->session->getToken());
    }

    function listBuildpacks(){
        
        // Prepare Response
        $response = new Response(Response::$RES_QUERY);
        
        // Set attributes
        /*$params = Array(
            'q' => 'space_guid:' . $spaceguid
        );*/
        
        try{
            // Retrieve data
            $buildpacks = $this->buildpacks->listBuildpacks();
            $response->setStatus(Response::$STAT_OK);
            $response->setData($buildpacks);
        } catch (Exception $ex) {
            // Handle errors
            $response->setStatus(Response::$STAT_ERROR);
            $response->setData($ex->getMessage());
        }

        // Display Respone
        $response->display();        
    }
    
}
