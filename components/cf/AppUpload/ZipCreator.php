<?php
namespace cf\AppUpload;

require_once("ResourcesInterface.php");


/**
 * Cloud foundry Apps Upload
 * @author: Carlos Frias <carlos.frias@ge.com>
 * @version: 1.0
 * @since: April 2016
 */
class ZipCreator implements ResourcesInterface {
	
	protected $path = "";
	protected $tempFile = "";
	protected $zip = null;
	protected $resources = array();
		
	public function __construct($options){
		$this->tempFile = $this->getTempFile();
		
		if(isset($options['path'])){
			$this->setPath($options['path']);
		}
	}
	
	public function __destructs(){
		$this->removeTempFile();
	}

	public function setPath($path){
		if(!is_dir($path)){
			throw new \Exception("Path '$path' is not a directory");
		}		
		$this->path = realpath($path);
		$this->createZip();
	}
	
	public function getResources(){
		return $this->resources;
	}
	
	private function createZip(){
		$this->zip = new \ZipArchive();
		$openResult = $this->zip->open($this->tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
		if($openResult !== TRUE){
			throw new \Exception("Cannot create temporary zip file");
		}
		
		// Create recursive directory iterator
		$files = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->path),
			\RecursiveIteratorIterator::LEAVES_ONLY
		);
		
		foreach ($files as $name => $file) {
			// Skip directories (they would be added automatically)
			if (!$file->isDir()){
				// Get real and relative path for current file
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($this->path) + 1);

				// Add current file to archive
				$this->zip->addFile($filePath, $relativePath);
				
				array_push($this->resources, array(
					"fn" => $relativePath,
					"size" => filesize($filePath),
					"sha1" => sha1_file($filePath)
				));
			}
		}

		// Zip archive will be created only after closing object
		$this->zip->close();
		echo $this->tempFile;
		
	}
	
	private function getTempFile(){
		$tempfile = tempnam(sys_get_temp_dir(), 'cf_');
		return $tempfile;
	}
	
	private function removeTempFile(){
		if(file_exists($this->tempFile)){
			unlink($this->tempFile);
		}
	}

}