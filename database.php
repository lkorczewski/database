<?php

//====================================================
// Łukasz Korczewski
// database class
//====================================================

class Database {
	
	const STATE_UNCONNECTED = 0;
	const STATE_CONNECTED = -1;
	const STATE_FAILED = 1;
	
	private $mysqli;
	private $state;
	
	private $host;
	private $port;
	private $user;
	private $password;
	private $database;
	
	function __construct(){
		$this->state = self::STATE_UNCONNECTED;
		$this->host = 'localhost';
		$this->port = '3306';
		$this->user = '';
		$this->password = '';
		$this->database = '';
	}
	
	//------------------------------------------------
	// setting host for connection
	//------------------------------------------------
	
	function set_host($host){
		$this->host = $host;
	}
	
	//------------------------------------------------
	// setting port for connection
	//------------------------------------------------
	
	function set_port($port){
		$this->port = $port;
	}
	
	//------------------------------------------------
	// setting username for connection
	//------------------------------------------------
	
	function set_user($user, $password = ''){
		$this->user = $user;
		if($password != ''){
			$this->password = $password;
		}
	}
	
	//------------------------------------------------
	// setting password for connection
	//------------------------------------------------
	
	function set_password($password){
		$this->password = $password;
	}
	
	//------------------------------------------------
	// setting database name for connection
	//------------------------------------------------
	
	function set_database($database){
		$this->database = $database;
	}
	
	//------------------------------------------------
	// establishing connection
	//------------------------------------------------
	
	function connect(){
		
		$this->mysqli = new mysqli(
			$this->host,
			$this->user,
			$this->password,
			$this->database,
			$this->port
		);
		
		if($this->mysqli->connect_errno) {
			$this->state - self::STATE_FAILED;
			$error = $this->mysqli->connect_error;
			return false;
		}
		
		$this->mysqli->set_charset('UTF8');
		
		$this->state = self::STATE_CONNECTED;
		return true;
	}

	//------------------------------------------------
	// query template 
	//------------------------------------------------
	
	private function _query($query){
		
		// connection not initialized
		if($this->state == self::STATE_UNCONNECTED){
			$this->connect();
		}
		
		// connection initialized, but failed
		if($this->state == self::STATE_FAILED){
			return false;
		}
		
		// query
		$result = $this->mysqli->query($query);
		
		// boolean result
		return $result;
		
	}

	//------------------------------------------------
	// executing query
	//------------------------------------------------
	
	function query($query){
		
		// query	
		$result = $this->_query($query);
		
		// boolean result
		if(is_bool($result))
			return $result;
		
		// rows in result
		$rows = array();
		while($row = $result->fetch_assoc()){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//------------------------------------------------
	// executing query
	//------------------------------------------------
	
	function execute($query){
		
		// query
		$result = $this->_query($query);
		
		// returning result
		if($result === false) return false;
		
		return true;
	}
	
	//------------------------------------------------
	// returning one row
	//------------------------------------------------
	
	function fetch_one($query){
		
		// query
		$result = $this->_query($query);
		
		// failure handling
		if($result === false) return false;
		
		// returning one row
		$row = $this->mysqli->fetch_assoc();
		
		return $row;
		
	}
	
	//------------------------------------------------
	// returning all rows
	//------------------------------------------------
		
	function fetch_all($query){
		
		// query
		$result = $this->_query($query);
		
		// failure handling
		if($result === false) return false;
		
		// returning all rows
		$rows = array();
		while($row = $result->fetch_assoc()){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//------------------------------------------------
	// informative functions
	//------------------------------------------------
	
	function get_affected_rows(){
		return $this->mysqli->affected_rows;
	}
	
	//------------------------------------------------
	// transaction
	//------------------------------------------------
	
	function start_transaction(){
		$this->mysqli->autocommit(FALSE);
	}
	
	function commit_transaction(){
		$this->mysqli->commit();
		$this->mysqli->autocommit(TRUE);
	}
	
	
	//------------------------------------------------
	// closing connection
	//------------------------------------------------
	
	function close(){
		$this->mysqli->close();
	}
	
}

?>
