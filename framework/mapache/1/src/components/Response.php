<?php

/*******************************************************************************
********************************************************************************
**** File Name: Response.php                                                ****
**** File type: PHP5 class                                                  ****
**** @version: 1.0                                                          ****
**** ---------------------------------------------------------------------- ****
**** @author: Víctor Hugo García Harzbecher                                 ****
**** Contact: mail to victorhugo.garcia@ge.com                              ****
**** Created on : February, 2015                                            ****
**** Description: Define the default object response for webservice         ****
********************************************************************************
*******************************************************************************/

namespace Mapache;

class Response {
    
    public static $RES_ACTION = 'action';// Default
    public static $RES_QUERY = 'query';
    public static $RES_ERROR = 'error';
    
    public static $STAT_OK = 'ok';
    public static $STAT_MALFORMED = 'malformed response';
    public static $STAT_ERROR = 'error';
    
    private $responseType = null;
    private $data = null;
    private $status = null;
    
    /**
     * Constructor
     * @param String $type Response type
     * @param String $data Response data
     */
    public function __construct($type = null, $data = null) {
        $this->status = self::$STAT_OK;
        $this->responseType = (isset($type))? $type : self::$RES_ACTION;
        $this->data = $data;
    }
    
    /**
     * Define response type
     * @param String $type
     */
    public function setType($type){
        $this->responseType = (isset($type))? $type : self::$RES_ACTION;
    }
    
    /**
     * Define response data
     * @param type $data
     */
    public function setData($data) {
        $this->data = $data;
    }
    
    /**
     * Set response status
     * @param String $status
     */
    public function setStatus($status){
        $this->status = $status;
    }
    
    /**
     * Display response as JSON
     */
    public function display(){
        
        $jsonData = json_encode($this->data);
        if(empty($jsonData) || !isset($jsonData)){$this->status = self::$STAT_MALFORMED;}
        
        $json = json_encode(array(
            'type' => $this->responseType,
            'status' => $this->status,
            'data' => $this->data
        ));
        header('Content-Type: application/json');
        echo $json;
    }
}
