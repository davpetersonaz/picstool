<?php
class DbFactory{

	public function delete($table, $values=array(), $where=false, $log=false){
		$return = 0;
		try{
			$query = 'DELETE FROM '.$table.($where?' WHERE '.$where:'');
			if($log){ $this->logQueryAndValues($query, $values, $log); }
			$this->stmt = $this->connection->prepare($query);
			foreach($values as $column=>$value){
				$this->stmt->bindValue(':'.$column, $value);
			}
			$this->stmt->execute();
			$return = $this->stmt->rowCount();
		}catch (PDOException $e){
			logDebug('ERROR: selecting: '.var_export($e, true));
			logDebug('ERROR: params: '.var_export($values, true));
		}
//		logDebug('delete returning: '.$return);
		return $return;
	}

	public function deleteQuery($query, $values=array(), $log=false){
		$return = 0;
		try{
			if($log){ $this->logQueryAndValues($query, $values, $log); }
			$this->stmt = $this->connection->prepare($query);
			foreach($values as $placeholder=>$value){
				$this->stmt->bindValue(':'.$placeholder, $value);
			}
			$this->stmt->execute();
			$return = $this->stmt->rowCount();
		}catch (PDOException $e){
			logDebug('ERROR: deleting: '.var_export($e, true));
			logDebug('ERROR: params: '.var_export($values, true));
		}
//		logDebug('delete effected '.$return.' rows');
		return $return;
	}

	public function insert($table, $values=array(), $log=false){
		$return = false;
		try{
			$query = 'INSERT INTO '.$table;
			$firstIteration = true;
			foreach($values as $column=>$value){
				$query .= ($firstIteration ? ' (' : ', ').$column;
				$firstIteration = false;
			}
			if(!$firstIteration){ $query .= ')'; }
			if(!empty($values)){ $query .= ' VALUES '; }
			$firstIteration = true;
			foreach($values as $column=>$value){
				$query .= ($firstIteration ? ' (' : ', ').':'.$column;
				$firstIteration = false;
			}
			if(!$firstIteration){ $query .= ')'; }
			if($log){ $this->logQueryAndValues($query, $values, $log); }
			$this->stmt = $this->connection->prepare($query);
			foreach($values as $column=>$value){
				$this->stmt->bindValue(':'.$column, $value);
			}
			if($this->stmt->execute() === false){
				logDebug('ERROR: executing query: '.$this->connection->errorCode().': '.var_export($this->connection->errorInfo(), true));
				$this->logQueryAndValues($query, $values, 'DbFactory->insert');
			}else{
				$return = $this->connection->lastInsertId();
			}
		}catch (PDOException $e){
			logDebug('ERROR: inserting: '.var_export($e, true));
			logDebug('ERROR: params: '.var_export($values, true));
		}
//		logDebug('insert returning: '.$return);
		return $return;
	}

	public function insertQuery($query, $values=array(), $log=false){
		$return = false;
		try{
			if($log){ $this->logQueryAndValues($query, $values, $log); }
			$this->stmt = $this->connection->prepare($query);
			foreach($values as $column=>$value){
				$this->stmt->bindValue(':'.$column, $value);
			}
			if($this->stmt->execute() === false){
				logDebug('ERROR: executing query: '.$this->connection->errorCode().': '.var_export($this->connection->errorInfo(), true));
				$this->logQueryAndValues($query, $values, 'DbFactory->insertQuery');
			}else{
				$return = $this->connection->lastInsertId();
			}
		}catch (PDOException $e){
			logDebug('ERROR: inserting: '.var_export($e, true));
			logDebug('ERROR: params: '.var_export($values, true));
		}
//		logDebug('insertQuery returning: '.$return);
		return $return;
	}

	public function select($query, $values=array(), $fetchModes=PDO::FETCH_ASSOC, $log=false){
		$return = array();
		try{
			if($log){ $this->logQueryAndValues($query, $values, $log); }
			$this->stmt = $this->connection->prepare($query);
			foreach($values as $column=>$value){
				$this->stmt->bindValue(':'.$column, $value);
			}
			$this->stmt->execute();
			if($fetchModes !== NULL){
				$rows = $this->stmt->fetchAll($fetchModes);
				if(count($rows) > 0){
					$return = $rows;
				}
			}
		}catch (PDOException $e){
			logDebug('ERROR: selecting: '.var_export($e, true));
			logDebug('ERROR: params: '.var_export($values, true));
		}
//		logDebug('select returning '.count($return).' rows');
		return $return;
	}

	//updates by tablename, columnvalues, and where-clause
	public function update($table, $values=array(), $where='', $log=false){
		$return = 0;
		$query = "UPDATE {$table} SET ";
		foreach(array_keys($values) as $column){
			$query .= "{$column}=:{$column}, "; 
		}
		$query = substr($query, 0, -2);//remove the last ", "
		if(!empty($where)){
			$query .= " WHERE {$where}";
		}
		if($log){ $this->logQueryAndValues($query, $values, $log); }
		try{
			$this->stmt = $this->connection->prepare($query);
			foreach($values as $placeholder=>$value){
				$this->stmt->bindValue(':'.$placeholder, $value);
			}
			$this->stmt->execute();
			$return = $this->stmt->rowCount();
		}catch (PDOException $e){
			logDebug('ERROR: updating: '.var_export($e, true));
			logDebug('ERROR: params: '.var_export($values, true));
		}
//		logDebug('update effected '.$return.' rows');
		return $return;
	}

	public function updateQuery($query, $values=array(), $log=false){
		$return = 0;
		try{
			if($log){ $this->logQueryAndValues($query, $values, $log); }
			$this->stmt = $this->connection->prepare($query);
			foreach($values as $placeholder=>$value){
				$this->stmt->bindValue(':'.$placeholder, $value);
			}
			$this->stmt->execute();
			$return = $this->stmt->rowCount();
		}catch (PDOException $e){
			logDebug('ERROR: updating: '.var_export($e, true));
			logDebug('ERROR: params: '.var_export($values, true));
		}
//		logDebug('update effected '.$return.' rows');
		return $return;
	}
	
	/////////////////////////
	//HELPER FUNCTIONS
	/////////////////////////

	public function dbSafe($data){
		// 1. remove white spaces (trim)
		// 2. remove any escape slashes put infront of quotes by magic quotes (stripslashes)
		// 3. encode all special characters i.e. double quotes, single quotes, ampersands, symbols etc (htmlentities) - no need to decode when pulling data out of database. browser will automatically use doctype to decode
		$safe_data = htmlentities(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
		return $safe_data;
	}

	//this returns the actual column name, it does not lowercase them or anything.
	public function getColumns($table){
		$return = array();
		$rows = $this->select("SHOW TABLES LIKE '{$table}'");
		if(!empty($rows)){
			$rows = $this->select("SHOW COLUMNS FROM {$table}", array(), PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
			$return = array_keys($rows);
		}
		return $return;
	}

	//TODO: SHOULD USE THIS WHENEVER BLINDLY UPDATING WITH AN ARRAY
	public function verifyColumns($table, $values){
		$result = true;
		$dontExist = array();
		$dbcolumns = $this->getColumns($table);
		$lowercase = array_map('strtolower', $dbcolumns);
		foreach(array_keys($values) as $column){
			if(!in_array(strtolower($column), $lowercase) && !in_array($column, $dbcolumns)){
				$result = false;
				Func::errorlog("column [{$column}] doesnt exist in table [{$table}]");
				$dontExist[] = $column;
			}
		}
		if(!$result){
			logDebug('dbcolumns: '.var_export($dbcolumns, true));
			logDebug("these columns do not exist in {$table}: ".implode(', ', $dontExist));
		}
		return $result;
	}

	public function getLastSequence($table, $show_id=false){
		$where = ($show_id ? ' WHERE show_id=:show_id' : '');
		$params = ($show_id ? array('show_id'=>$show_id) : array());
		if($show_id){
			$params = array('show_id'=>$show_id);
		}
		$sequence = 0;
		$rows = $this->select('SELECT MAX(sequence) AS sequence FROM '.$table.$where, $params);
		if($rows[0]['sequence']){
			$sequence = $rows[0]['sequence'];
		}
//		logDebug('getLastSequence returning: '.$sequence);
		return $sequence;
	}

	public function replicateFetchColumnGroup($rows){
		$returnArray = array();
		foreach($rows as $row){
			$returnArray[$row[0]] = array(0=>$row[1]);
		}
		return $returnArray;
	}

	/**
	 * this will take an array create a new one using the given column as the index,
	 * note: if the given column is not unique, only the last value for each index will be preserved (it'll overwrite the previous)
	 * 
	 * @param type $array input array
	 * @param type $column column to index on
	 * @return array[column]=>row;
	 */
	public function indexArrayByColumn($array, $column){
		$return = array();
		if(count($array) > 0){
			foreach($array as $row){
				$return[$row[$column]] = $row;
			}
		}
		uksort($return, 'strnatcasecmp');
		return $return;
	}

	/**
	 * this will take an array create a new one using the given column as the index,
	 * note: if the given column is not unique, only the last value for each index will be preserved (it'll overwrite the previous)
	 * 
	 * @param type $array input array
	 * @param type $column column to index on
	 * @return array[column]=>row;
	 */
	public function indexArrayByColumns($array, $columns, $delimiter=','){
		$return = array();
		if(count($array) > 0){
			foreach($array as $row){
				$index = '';
				foreach($columns as $column){
					$index .= (empty($index) ? '' : $delimiter).$row[$column];
				}
				$return[$index] = $row;
			}
		}
		uksort($return, 'strnatcasecmp');
		return $return;
	}

	/**
	 * this will take an array and create a new one using the given column as the base of an array, i.e. -- output[column]=>array(row);
	 * similar to indexArrayByColumn, except this will have an array under each index value
	 * 
	 * @param type $array input array
	 * @param type $column column to index on
	 * @return output[column]=>array(row);
	 */
	public function indexArrayGroupingsByColumn($array, $column){
		$return = array();
		if(count($array) > 0){
			foreach($array as $row){
				if(!isset($return[$row[$column]])){
					$return[$row[$column]] = array();
				}
				$return[$row[$column]][] = $row;
			}
		}
		return $return;
	}

	public function logQueryAndValues($query, $values=array(), $callingFunction=false){
//		logDebug('logQueryAndValues: '.var_export($values, true));
		if($values){
			foreach($values as $key=>$value){
				if(substr($key, 0, 1) !== ':'){ $key = ":{$key}"; }
//				logDebug("key: [{$key}]");
				$occurences = Func::strpos_all($query, $key);
				foreach($occurences as $pos){
					$posNextCharAfterKey = $pos + strlen($key);
					$nextCharAfterKey = substr($query, $posNextCharAfterKey, 1);
//					logDebug("pos[{$pos}], posNextCharAfterKey[{$posNextCharAfterKey}]: [{$nextCharAfterKey}] ord[".ord($nextCharAfterKey)."][".chr(ord($nextCharAfterKey))."], should be: ".ord(' '));
					if(strlen($query) === $posNextCharAfterKey //if the end of the key is the end of the querystring
						|| in_array($nextCharAfterKey, array(',', ' ', ')', "\t", "\r", "\n", "\r\n"))){//if the next char after the key is a comma or space
							//now we know we found the exact key and not just a close match
//							logDebug("replacing key [{$key}] at [{$pos}] for [".strlen($key)."] with value [{$value}]");
							$query = substr_replace($query, "'{$value}'", $pos, strlen($key));
							break;
					}
				}
			}
		}
		logDebug(($callingFunction ? $callingFunction.': ' : '').$query);
	}
	
	public function close(){
		if($this->stmt){ $this->stmt->closeCursor(); }
		$this->stmt = NULL;
		$this->connection = NULL;
	}
	
	public static function getInstance(){
		if (self::$instance === false){
			self::$instance = new DbFactory();
		}else{
//			logDebug('using current DbFactory, no instantiation!!!');
		}
		return self::$instance;
	}

	protected function __construct(){
//		logDebug('creating DbFactory');
		$this->connection = new PDO(
			'mysql:host='.DB_HOST.';port=3306;dbname='.DB_NAME.';charset=utf8', 
			DB_USER, 
			DB_PASS, 
			array(
				PDO::MYSQL_ATTR_FOUND_ROWS => true,
				PDO::ATTR_PERSISTENT => false,
				PDO::ATTR_CASE => PDO::CASE_LOWER
			)
		);
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
	}

	private static $instance = false;
	private $connection = false;
	private $stmt = false;
}