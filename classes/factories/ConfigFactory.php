<?php
class ConfigFactory{
	public function newInstance(){
		$config = new Config();
		$config->init();
		return $config;
	}
}