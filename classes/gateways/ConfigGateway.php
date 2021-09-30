<?php
class ConfigGateway extends AbstractGateway{
	
	public function getConfig($site_number){
		$query = "SELECT site_number, browser_title, homepage_header, homepage_description
					FROM config 
					WHERE site_number=:site_number";
		$values = array('site_number'=>$site_number);
//		$this->db->logQueryAndValues($query, $values, 'getConfig');
		$rows = $this->db->select($query, $values);
		return (isset($rows[0]) ? $rows[0] : false);
	}
	
	public function updateConfig($values){
		return $this->db->update('config', $values, 'site_number='.SITE_NUMBER, 'updateConfig');
	}

}