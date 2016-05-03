<?php

namespace cf\AppUpload;

require_once("ResourcesInterface.php");

/**
 * Cloud foundry Apps Upload
 * @author: Carlos Frias <carlos.frias@ge.com>
 * @version: 1.0
 * @since: April 2016
 */
class ZipInfo implements ResourcesInterface {

	protected $zipFileName = "";
	protected $tempDir;
	protected $zip = null;
	
    public function __construct($options = array()){
		if(isset($options['filename'])){
			$this->setZipFilename($options['filename']);
		}
    }
	
	public function __destruct(){
		$this->close();
	}
	
	public function setZipFilename($file = ""){
		if(!file_exists($file)){
			throw new \Exception("Zip file '$file' not found");
		}
		$this->zipFileName = $file;
		$this->initZipArchive();
	}
	
	public function getResources(){
		$resources = array();
		for($i = 0; $i<$this->zip->numFiles; $i++){
			$info = $this->zip->statIndex($i);
			if($info['size'] > 0){
				$resources[$i] = array(
					"fn" => $info['name'],
					"size" => $info['size'],
					"sha1" => $this->getSha1Hash($this->tempDir . "/" . $info['name'])
				);
			}
		}
		return $resources;
	}
	
	public function close(){
		if($this->zip !== null){
			$this->zip->close();
		}
		if($this->tempDir != "") {
			$this->removeTempDir($this->tempDir);
		}
	}

	private function initZipArchive() {
		$this->zip = new \ZipArchive();
		if($this->zip->open($this->zipFileName) !== TRUE) {
			throw new \Exception("Zip file '$file' not found");
		}
		if(! $this->extractFiles() ) {
			throw new \Exception("Could not extract zip contents.");
		}
	}
		
	private function extractFiles() {
		$tempDir = $this->getTempDir();
		return $this->zip->extractTo($tempDir);
	}
	
	private function getSha1Hash($filename){
		$sha1 = "";
		if(file_exists($filename)){
			$sha1 = sha1_file($filename);
		}
		else{
			throw new \Exception("File '$file' not found for generating sha1 hash");
		}
		return $sha1;
	}
	
	
	/**
	* Temp Dir functions
	**/
	
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
	
}

