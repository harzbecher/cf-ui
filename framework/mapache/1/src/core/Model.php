<?php

namespace Mapache;
/**
 * Orion Application API
 * @version 1.0
 * @author Víctor Hugo García Harzbecher <victorhugo.garcia@ge.com>
 * @since September, 2015
 */
class Model extends \ADODB_Active_Record{
    
    function __construct() {
        parent::__construct();
    }
    
    public function print_pre($object){
        echo "<pre>";
        print_r($object);
        echo "</pre>";
    }
    
    public function importModel($module, $modelName){
        if(isset($module))
        {
            $modelPath = 'src/'.$module.'/models/'.$modelName.'.php';
            if(file_exists($modelPath))
            {
                require $modelPath;
                return new $modelName;
            }else throw new Exception('Selected model is not defined for this module');
        }else throw new Exception('Can not load import model, the module is not defined');
    }

}
?>
