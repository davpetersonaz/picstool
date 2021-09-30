<?php
class Config{
	
	private $regex = "/^\<h(\d)\>(.*)\<\/h\d\>[\s\S\ ]*\<h(\d)\>(.*)\<\/h\d\>[\s\S\ ]*[<h(\d)\>(.*)\<\/h\d\>]?$/";

	public function getSiteTitle(){
		return $this->site_title;
	}
	
//	public function getHomepageHeader($line=false){
//		$return = false;
//		if($line !== false){
//			if(preg_match($this->regex, $this->homepage_header, $matches) === 1 && isset($matches[0])){
//				if(isset($matches[$line*2])){
//					$return = $matches[$line*2];
//				}
//			}
//		}else{
//			$return = $this->homepage_header;
//		}
//		return $return;
//	}
	
	public function getHomepageHeader(){
		$header = $this->getHomepageHeaderArray();
		return $header['full'];
	}
	
	public function getHomepageHeaderArray(){
//		logDebug('getHomepageHeaderArray');
		$headnum = 0;
		$hval = $head = array_fill(0, 3, false);
		$ongoing = $original = $this->homepage_header;
//		logDebug('original: '.$ongoing);
		while(($headStartPos = strpos($ongoing, '<h')) !== false){
			$hval[$headnum] = intval(substr($ongoing, $headStartPos+2, 1));
			$ongoing = substr($ongoing, -$headStartPos+4);//remove the heading start-tag and anything before that.
//			logDebug('ongoing: '.$ongoing);
			$headEndPos = strpos($ongoing, "</h{$hval[$headnum]}>");
			$head[$headnum] = substr($ongoing, 0, $headEndPos);
			$ongoing = substr($ongoing, $headEndPos+5);//remove the heading and the end-tag
//			logDebug('ongoing: '.$ongoing);
//			logDebug("hval{$headnum}: {$hval[$headnum]}, head{$headnum}: {$head[$headnum]}");
//			logDebug('end of loop');
			$headnum++;
		}
		$header = array('hval'=>$hval, 'head'=>$head, 'full'=>$original);
//		logDebug('header: '.var_export($header, true));
		return $header;
	}
	
	public function getHomepageDescription(){
		return $this->homepage_description;
	}
	
	public function updateConfig($values){
		return $this->configGateway->updateConfig($values);
	}
	
	public function init(){
		$this->configGateway = new ConfigGateway();
		$this->config = $this->configGateway->getConfig(SITE_NUMBER);
		$this->site_number = $this->config['site_number'];
		$this->site_title = $this->config['browser_title'];
		$this->homepage_header = $this->config['homepage_header'];
		$this->homepage_description = $this->config['homepage_description'];
	}
	
	public function __toString(){
//		logDebug('tostring called');
		try{
			$array = array(
				'site_number' => $this->site_number,
				'site_title' => $this->site_title,
				'homepage_header' => Func::stripTagsPlus($this->homepage_header),
				'homepage_description' => Func::stripTagsPlus($this->homepage_description)
			);
			return var_export($array, true);
		}catch(Exception $e){
			return $e;
		}
	}
	
	private $configGateway = false;
	private $config = false;
	private $site_number = '';
	private $site_title = '';
	private $homepage_header = '';
	private $homepage_description = '';
}