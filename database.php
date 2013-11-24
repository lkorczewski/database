<?php

//====================================================
// database class
//====================================================

class Database {
	
	const STATE_UNCONNECTED = 0;
	const STATE_CONNECTED = 1;
	const STATE_FAILED = -1;
	
	// connection parameters
	private $host = 'localhost';
	private $port = '3306';
	private $user = '';
	private $password = '';
	private $database = '';
	
	// inner parameters
	private $mysqli;
	private $state = self::STATE_UNCONNECTED;
	private $last_error = false;
	
	function __construct($parameters = false){
		if($parameters){
			if(isset($parameters['host']))
				$this->host = $parameters['host'];
			if(isset($parameters['port']))
				$this->port = $parameters['port'];
			if(isset($parameters['user']))
				$this->user = $parameters['user'];
			if(isset($parameters['password']))
				$this->password = $parameters['password'];
			if(isset($parameters['databse']))
				$this->database = $parameters['database'];	
		}
	}
	
	//------------------------------------------------
	// setting host for connection
	//------------------------------------------------
	// Deprecated!
	//------------------------------------------------
	
	function set_host($host){
		$this->host = $host;
	}
	
	//------------------------------------------------
	// setting port for connection
	//------------------------------------------------
	// Deprecated!
	//------------------------------------------------
	
	function set_port($port){
		$this->port = $port;
	}
	
	//------------------------------------------------
	// setting username for connection
	//------------------------------------------------
	// Deprecated!
	//------------------------------------------------
	
	function set_user($user, $password = false){
		$this->user = $user;
		if($password !== false){
			$this->password = $password;
		}
	}
	
	//------------------------------------------------
	// setting password for connection
	//------------------------------------------------
	// Deprecated!
	//------------------------------------------------
	
	function set_password($password){
		$this->password = $password;
	}
	
	//------------------------------------------------
	// setting database name for connection
	//------------------------------------------------
	// Deprecated!
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
			$this->state = self::STATE_FAILED;
			$this->last_error = [
				'number'     => $this->mysqli->connect_errno,
				'message'    => $this->mysqli->connect_error,
				'backtrace'  => debug_backtrace(FALSE),
			];
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
		if($this->mysqli->error){
			$this->last_error = [
				'number'     => $this->mysqli->errno,
				'message'    => $this->mysqli->error,
				'backtrace'  => debug_backtrace(FALSE),
			];
		}
		
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
		$rows = [];
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
		$row = $result->fetch_assoc();
		
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
		$rows = [];
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
	// error handling
	//------------------------------------------------
	
	function get_last_error(){
		return $this->last_error;
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
	
	function rollback_transaction(){
		$this->mysqli->rollback();
		$this->mysqli->autocommit(TRUE);
	}
	
	//------------------------------------------------
	// string escaping
	//------------------------------------------------
	
	function escape_string($string){
		return $this->mysqli->real_escape_string($string);
	}
	
	//------------------------------------------------
	// closing connection
	//------------------------------------------------
	
	function close(){
		$this->mysqli->close();
	}
	
}

?>
