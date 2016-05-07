<?php
use Mapache\Controller;
use cf\AppUpload;

require_once APPLICATION_PATH . '/components/cf/cf_curl.php';
require_once APPLICATION_PATH . '/components/cf/AppUpload/ZipCreator.php';
require_once APPLICATION_PATH . '/components/Flow/Autoloader.php';
class UploadApps extends Controller
{
    public function __construct(){
        parent::__construct();
		$this->session = new Session();
        if(!$this->session->isActive()){
            header('Location:/cf-ui/login/');exit;
        }
        
        \Flow\Autoloader::register(realpath( APPLICATION_PATH . '/components' ));
        if(!isset($_SESSION['cfUpload'])){
            $_SESSION['cfUpload'] = array(
                'tempChunkUploadDir' => $this->getTempDir(),
                'tempUploadDir' => $this->getTempDir(),
                'uploadInProgress' => true,
            );
        }
    }

    function indexAction($guid = ''){
        
    }
    
    function uploadAction($guid = '')
    {
        $config = new \Flow\Config();
        $config->setTempDir($_SESSION['cfUpload']['tempChunkUploadDir']);
        $request = new \Flow\Request();
        $filename = preg_replace('/\.\.(\\|\/)/', "", $request->getRelativePath());
        $this->createRelativeTree($_SESSION['cfUpload']['tempUploadDir'], $filename);
        if (\Flow\Basic::save($_SESSION['cfUpload']['tempUploadDir'] . "/{$filename}", $config, $request)) {
            // file saved successfully and can be accessed at './final_file_destination'
            echo "Done.";
        } else {
             // This is not a final chunk or request is invalid, continue to upload.
        }
    }

    function CompleteUploadAction($guid = ''){
        $response = new \Mapache\Response(\Mapache\Response::$RES_QUERY);
        
        if($_SESSION['cfUpload']['uploadInProgress'] && $guid != '' ){
            $endpoint = $this->session->getEndPoint();
            $token = $this->session->getToken();
            
            $http = new \cf\cf_curl($endpoint . "/v2/apps/{$guid}/bits");
            $http->setMethod(\cf\cf_curl::$METHOD_PUT);
            $http->setToken($token);
            
            $zipInfo = new \cf\AppUpload\ZipCreator(array("path" => $_SESSION['cfUpload']['tempUploadDir']));
            $args = array(
                "async" => "true",
                "resources" => json_encode($zipInfo->getResources(), JSON_UNESCAPED_SLASHES),
                "application" => new CURLFile($zipInfo->getZipPath(), 'application/zip', 'application.zip')
            );
            
            $http->setParameters($args, 'raw');
            $http->setParseMode(\cf\cf_curl::$PMODE_RAW);
            $cfResponse = $http->execute();            
            $response->setData($cfResponse);
            
            $this->removeTempDir($_SESSION['cfUpload']['tempUploadDir']);
            $this->removeTempDir($_SESSION['cfUpload']['tempChunkUploadDir']);
            $_SESSION['cfUpload'] = array();
            unset($_SESSION['cfUpload']);
        }
        else{
            $response->setType(\Mapache\Response::$RES_ERROR);
            $response->setData(array("description" => "No upload in progress"));
        }
        $response->display();
    }
    
    
    
    private function getTempDir(){
		$tempfile = tempnam(sys_get_temp_dir(), 'cf_');
		if (file_exists($tempfile)) {
			unlink($tempfile);
		}
		mkdir($tempfile);
		if (!is_dir($tempfile)) { 
			throw new \Exception("Cannot create tmp dir to extract zip contents");
		}
		$this->tempDir = $tempfile;
		return $tempfile;
	}
	
	private function removeTempDir($dir){
		$files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) {
		  if(is_dir("$dir/$file")) {
			  $this->removeTempDir("$dir/$file");
		  }
		  else {
			  unlink("$dir/$file");
		  }
		}
		return rmdir($dir); 
	}
    
    private function createRelativeTree($basepath, $filename = ''){
        $directories = explode("/", $filename);
        unset($directories[(count($directories) - 1)]);
        $path = $basepath . '/' . implode("/", $directories);        
        if(!file_exists($path)){
            mkdir($path, 0777, true);
        }
    }
    

}

