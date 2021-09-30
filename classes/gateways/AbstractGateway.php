<?php
abstract class AbstractGateway{

	public function __construct(){
		$this->db = DbFactory::getInstance();
	}
	
	public function indexArrayByColumn($array, $column){
		return $this->db->indexArrayByColumn($array, $column);
	}
	
	public function logQueryAndValues($query, $values=array(), $callingFunction=false){
		$this->db->logQueryAndValues($query, $values, $callingFunction);
	}

	protected $db = false;
	
}