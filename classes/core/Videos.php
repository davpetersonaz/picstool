<?php
class Videos{
	public function __construct(){
		$this->files_array = scandir(self::VIDS_DIR);
		$this->files_array = array_diff($this->files_array, array('.', '..', 'temp', 'thumbs'));
	}
	
	public function getVideos(){
		shuffle($this->files_array);
		return $this->files_array;
	}
	
	public function __toString() {
		try{
			$array = array(
				'files_array' => $this->files_array,
				'VIDS_DIR' => self::VIDS_DIR
			);
			return $array;
		}catch(Exception $e){
			return $e;
		}
	}
	
	const VIDS_DIR = REAL_PATH.'public_html/images/vids/';//this should probably go in config.php
	private $files_array = [];
}