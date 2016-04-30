<?php



namespace cf;
use Mapache\Exception;

/**
 * Cloud foundry http Service
 * @author: Víctor Hugo garcía Harzbecher <victorhugo.garcia@ge.com >
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

    private $config = Array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HEADER => 0,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_HTTPHEADER => Array(
            'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
            'Accept: application/json;charset=utf-8'
        )
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

    public function setParameters($parameters){
        if(is_array($parameters)){
            $this->parameters = $parameters;
        }
    }

    public function appendHeaders($headers){
        // Transform to array
        if(!is_array($headers)){
            $headers = array($headers);
        }

        foreach ($headers as $header){
          $this->config[CURLOPT_HTTPHEADER][count($this->config[CURLOPT_HTTPHEADER])] = $header;
        }
    }

    public function execute(){

        if(!isset($this->config[CURLOPT_URL]) || empty($this->config[CURLOPT_URL])){
            throw new Exception("Could not complete this request without a target URL");
        }

        /**
         * Methods and content type editions
         * @author Fernando Espinosa <fernando.espinosa@ge.com>
         */
        if(isset($this->parameters)){
            switch ($this->method){
                case self::$METHOD_GET:
                    $this->config[CURLOPT_URL] .= '?'.http_build_query($this->parameters);
                    break;
                case self::$METHOD_POST:
                    $this->config[CURLOPT_POST] = 1;
                    // http_build_query only used when a token is requested
                    if(isset($this->parameters['username']) && isset($this->parameters['password'])){
                        $this->config[CURLOPT_POSTFIELDS] = http_build_query($this->parameters);
                    }else{
                        $this->config[CURLOPT_POSTFIELDS] = json_encode($this->parameters);
                    }
                    break;
                case self::$METHOD_PUT:
                    $this->config[CURLOPT_CUSTOMREQUEST] = 'PUT';
                    $this->config[CURLOPT_POSTFIELDS] = json_encode($this->parameters);
                    //$this->config[CURLOPT_POSTFIELDS] = http_build_query($this->parameters);
                    break;
                case self::$METHOD_DELETE:
                    $this->config[CURLOPT_CUSTOMREQUEST] = "DELETE";
                    $this->config[CURLOPT_POSTFIELDS] = json_encode($this->parameters);
                    //$this->config[CURLOPT_POSTFIELDS] = http_build_query($this->parameters);
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
