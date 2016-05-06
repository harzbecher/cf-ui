<?php



namespace cf;
use Mapache\Exception;

/**
 * Cloud foundry http Service
 * @author: Víctor Hugo garcía Harzbecher <victorhugo.garcia@ge.com >
 * @author: Fernando Espinosa <fernando.espinosa@ge.com>
 * @author: Carlos Frias <carlos.frias@ge.com>
 * @version: 1.0
 * @since: April 2016
 */
class cf_curl{

    public static $METHOD_POST = 'POST';
    public static $METHOD_GET = 'GET';
    public static $METHOD_PUT = 'PUT';
    public static $METHOD_DELETE = 'DELETE';
    
    public static $PMODE_JSON = 'json';
    public static $PMODE_RAW = 'raw';

    private $method = null;
    private $parameters = null;
    private $parseMode = 'json';

    private $parametersFormat = 'json';
    private $possibleParametersFormats = array('json', 'http_query', 'raw');

    private $config = Array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HEADER => 0,
        CURLOPT_FOLLOWLOCATION => 1,
    );
    

    public function __construct($url = null, $method = null){

        // Set URL
        if(isset($url)){$this->setUrl($url);}
        // Set Method
        if(isset($method)){$this->setMethod($method);}

        // Set Proxy
        if( ($proxy = getGlobal('proxy')) !== null ){
            $this->config[CURLOPT_PROXY] = $proxy;
        }

    }
    
    public function setParseMode($mode){
        $this->parseMode = $mode;
        return true;
    }

    public function setCurlOptConfig($configArray){
        // Overwrite configuration
        foreach ($configArray as $opt => $value){
            $this->config[$opt] = $value;
        }
    }

    public function setUrl($url){
        if(isset($url) && !empty($url)){
            $this->config[CURLOPT_URL] = $url;
        } else {
            return false;
        }

        return true;
    }

    public function setMethod($method){
        $this->method = (isset($method))? $method : self::$METHOD_GET;
        return $this->method;
    }

    public function setParameters($parameters, $formatInto = "json"){
        if(in_array($formatInto, $this->possibleParametersFormats)){
            $this->parametersFormat = $formatInto;
        }
        
        $this->parameters = $parameters;
    }

    public function appendHeaders($headers){
        // Transform to array
        if(!is_array($headers)){
            $headers = array($headers);
        }
        
        if(!isset($this->config[CURLOPT_HTTPHEADER])){
            $this->config[CURLOPT_HTTPHEADER] = array();
        }

        foreach ($headers as $header){
            $this->config[CURLOPT_HTTPHEADER][] = $header;
        }
    }

    public function setToken($token){
         $this->appendHeaders("Authorization: bearer {$token}");
    }
    
    private function getFormattedParameters(){
        $formatted = "";
        switch($this->parametersFormat){
            case 'json':
                $formatted = json_encode($this->parameters);
                break;
            case 'http_query':
                $formatted = http_build_query($this->parameters);
                break;
            case 'raw':
            default:
                $formatted = $this->parameters;
                break;
        }
        return $formatted;
    }
    
    public function execute(){

        if(!isset($this->config[CURLOPT_URL]) || empty($this->config[CURLOPT_URL])){
            throw new Exception("Could not complete this request without a target URL");
        }
        
        /**
         * @author Victor garcia <victorhugo.garcia@ge.com>
         */
        switch($this->method){
            case self::$METHOD_GET:
                break;
            case self::$METHOD_POST:
                $this->config[CURLOPT_POST] = true;
                break;
            case self::$METHOD_PUT:
                $this->config[CURLOPT_CUSTOMREQUEST] = 'PUT';
                break;
            case self::$METHOD_DELETE:
                $this->config[CURLOPT_CUSTOMREQUEST] = "DELETE";
                break;
        }

        /**
         * Methods and content type editions
         * @author Fernando Espinosa <fernando.espinosa@ge.com>
         */
        if(isset($this->parameters)){
            switch ($this->method){
                case self::$METHOD_GET:
                    if(!preg_match('/\?/', $this->config[CURLOPT_URL])){
                        $this->config[CURLOPT_URL] .= '?';
                    }
                    $this->config[CURLOPT_URL] .= http_build_query($this->parameters);
                    break;
                case self::$METHOD_POST:
                    $this->config[CURLOPT_POSTFIELDS] = $this->getFormattedParameters();
                    break;
                case self::$METHOD_PUT:
                    $this->config[CURLOPT_POSTFIELDS] = $this->getFormattedParameters();
                    break;
                case self::$METHOD_DELETE:
                    $this->config[CURLOPT_POSTFIELDS] = $this->getFormattedParameters();
                    break;
            }
        }

        // start cURL request
        $handler = curl_init();
        curl_setopt_array($handler, $this->config);



        // Execute request and get response
        if (!($response = curl_exec($handler))) {
            throw new Exception(curl_error($handler));
        }

        // Close cURL
        curl_close($handler);

        // Parse response
        switch ($this->parseMode){
            case self::$PMODE_RAW:
                return $response;
            // Return response in PHP readable way
            default :
                return json_decode($response);
        }
        
        
    }

}
