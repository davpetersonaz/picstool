<?php
class UserGateway extends AbstractGateway{

	public function verifyUser($username, $password){
		$hashed = md5($password);
		$query = "SELECT user_id, uid 
					FROM users 
					WHERE uid=:username 
						AND pwd=:password";
		$values = array('username'=>$username, 'password'=>$hashed);
		$this->logQueryAndValues($query, $values, 'verifyUser');
		$rows = $this->db->select($query, $values);
		return (isset($rows[0]['user_id']) ? $rows[0]['user_id'] : false);
	}
	
}