<?php
class MiscGateway extends AbstractGateway{

	public function genericDelete($query, $values=array()){
		$rowAffected = $this->db->deleteQuery($query, $values);
		return $rowAffected;
	}
	
	public function genericInsert($query, $values=array()){
		$lastInsertId = $this->db->insertQuery($query, $values);
		return $lastInsertId;
	}

	public function genericSelect($query, $values=array(), $fetchModes=PDO::FETCH_ASSOC){
		$rows = $this->db->select($query, $values, $fetchModes);
		return $rows;
	}
	
	public function genericUpdate($query, $values=array()){
		$rowAffected = $this->db->updateQuery($query, $values);
		return $rowAffected;
	}
	
	public function setLongQueryTime($seconds){
		$query = "SET @@session.long_query_time=".intval($seconds);
		$rowsAffected = $this->db->updateQuery($query);
		return $rowsAffected;
	}

	public function setMysqlVariable($var, $val){
		$query = "SET SESSION {$var} = {$val}";
		$rowsAffected = $this->db->updateQuery($query);
		return $rowsAffected;
	}

}