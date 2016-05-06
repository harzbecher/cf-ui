<?php


/**
 * Cloud foundry route Service
 * @author Victor Hugo Garcia Harzbecher <victorhugo.garcia@ge.com>
 * @version: 1.0
 * @since: April 2016
 */
use Mapache\Controller;

class Routes extends Controller
{
    private $session = null;

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
        // Create session
        $this->session = new Session();
        if(!$this->session->isActive()){
            echo $this->session->getErrorMessage();
            header('Location:login');
        }
    }

    /**
     * List available spaces for defined user
     */
    function create(){
        
        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        
        try{
            // Read json post
            $json = file_get_contents('php://input');
            $routeData = json_decode($json);
            
            $args = Array();
            // Serialize parameters
            if(isset($routeData)){
                foreach($routeData as $name => $value)
                $args[$name] = $value;
            }

            // Prepare Space object
            $cfRoute = new cf\Routes(
                $this->session->getEndPoint(), 
                2, 
                $this->session->getToken());


            // Get Response
            $response->setData($cfRoute->add($args));
            
        } catch (Exception $ex) {

        }
        
       
        $response->display();
    }

}
