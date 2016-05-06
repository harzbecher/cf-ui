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
class Apps extends Controller
{

    private $session = null;
    private $cfApps = null;
    private $cfApps3 = null;

    public function __construct(){
        parent::__construct();
        $this->session = new Session();
        if(!$this->session->isActive()){
            echo $this->session->getErrorMessage();
            header('Location:login');
        }

        // Initialize Apps API Client
        $this->cfApps = new cf\Apps(
            $this->session->getEndPoint(),
            2,
            $this->session->getToken());

        $this->cfApps3 = new cf\Apps(
            $this->session->getEndPoint(),
            3,
            $this->session->getToken());
    }

    function indexAction(){
        //$this->view->render('Example_view');
        $cfApps3 = new cf\Apps(
            $this->session->getEndPoint(),
            3,
            $this->session->getToken());
    }

    function listApps($spaceguid){

        // Validate inputs
        if(!isset($spaceguid) || empty($spaceguid)){
            throw new Exception('Invalid space guid provided');
        }

        // Prepare Response
        $response = new Response(Response::$RES_QUERY);

        // Set attributes
        $params = Array(
            'q' => 'space_guid:' . $spaceguid
        );

        try{
            // Retrieve data
            $applications = $this->cfApps->listApps($params);
            $response->setStatus(Response::$STAT_OK);
            $response->setData($applications);
        } catch (Exception $ex) {
            // Handle errors
            $response->setStatus(Response::$STAT_ERROR);
            $response->setData($ex->getMessage());
        }

        // Display Respone
        $response->display();
    }

    public function getEnv($appguid){

        // Validate inputs
        if(!isset($appguid) || empty($appguid)){
            throw new Exception('Invalid application guid provided');
        }

        // Prepare Response
        $response = new Response(Response::$RES_QUERY);

        try{
            // Retrieve data
            $applications = $this->cfApps->getEnv($appguid);
            $response->setStatus(Response::$STAT_OK);
            $response->setData($applications);
        } catch (Exception $ex) {
            // Handle errors
            $response->setStatus(Response::$STAT_ERROR);
            $response->setData($ex->getMessage());
        }

        // Display Respone
        $response->display();

    }


    function add(){

        // Read json post
        $json = file_get_contents('php://input');
        $applicationData = json_decode($json);

        $params = Array();


        // Serialize attributes into params
        foreach($applicationData->attributes as $attribute){
            if(isset($attribute->value) && !empty($attribute->value)){
                $params[$attribute->name] = $attribute->value;
            }
        }

        //print_r($params);
        //exit;

        // Prepare Response
        $response = new Response(Response::$RES_QUERY);

        try{
            // Retrieve data
            $status = $this->cfApps->createApp($params);
            $response->setStatus(Response::$STAT_OK);
            $response->setData($status);
        } catch (Exception $ex) {
            // Handle errors
            $response->setStatus(Response::$STAT_ERROR);
            $response->setData($ex->getMessage());
        }

        // Display Respone
        $response->display();
    }


    function listFiles(){

        $guid = filter_input(INPUT_GET, 'guid');
        $instanceIndex = filter_input(INPUT_GET, 'instance_index');
        $path = filter_input(INPUT_GET, 'path');


        $cfFiles = new cf\Files(
                $this->session->getEndPoint(),
                2,
                $this->session->getToken());

        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($cfFiles->ls($guid, $instanceIndex, $path));
        $response->display();
    }



    function deleteApp(){
        $token = filter_input(INPUT_POST, 'token');
        $appguid = filter_input(INPUT_POST, 'appguid');

        $params = Array(
        );

        $cfApps = new cf\Apps($this->endPoint, 2, $token);

        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($this->cfApps->deleteApp($params, $appguid));
        $response->display();
    }

    function updateApp(){
        $token = filter_input(INPUT_POST, 'token');
        $appguid = filter_input(INPUT_POST, 'appguid');
        //$name = filter_input(INPUT_POST, 'appname');
        $state = filter_input(INPUT_POST, 'state');

        $params = Array(
            "state" => $state
        );

        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($this->cfApps->updateApp($params, $appguid));
        $response->display();
    }

    function restageApp(){
        $appguid = filter_input(INPUT_POST, 'appguid');

        $params = Array(
        );

        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        $response->setData($this->cfApps->restageApp($params, $appguid));
        $response->display();
    }
    
    function addRoute(){
        
         // Read json post
        $appguid = filter_input(INPUT_GET, 'app_guid');
        $routeguid = filter_input(INPUT_GET, 'route_guid');
        
        // Prepare Response
        $response = new Response(Response::$RES_QUERY);
        
        try{
            // Retrieve data
            $status = $this->cfApps->addRoute($routeguid, $appguid);
            $response->setStatus(Response::$STAT_OK);
            $response->setData($status);
        } catch (Exception $ex) {
            // Handle errors
            $response->setStatus(Response::$STAT_ERROR);
            $response->setData($ex->getMessage());
        }

        // Display Respone
        $response->display();
    }

}
