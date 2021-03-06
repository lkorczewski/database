<?php

namespace Database;

use MySQLi;

class Database {
	
	const STATE_UNCONNECTED  = 0;
	const STATE_CONNECTED    = 1;
	const STATE_FAILED       = -1;
	
	// connection parameters
	private $host      = 'localhost';
	private $port      = '3306';
	private $user      = '';
	private $password  = '';
	private $database  = '';
	
	// inner parameters
	private $mysqli;
	private $state = self::STATE_UNCONNECTED;
	private $last_error = false;
	
	function __construct(array $parameters = null){
		if($parameters){
			if(isset($parameters['host']))
				$this->host = $parameters['host'];
			if(isset($parameters['port']))
				$this->port = $parameters['port'];
			if(isset($parameters['user']))
				$this->user = $parameters['user'];
			if(isset($parameters['password']))
				$this->password = $parameters['password'];
			if(isset($parameters['database']))
				$this->database = $parameters['database'];
		}
	}
	
	//------------------------------------------------
	// establishing connection
	//------------------------------------------------
	
	function connect(){
		
		$this->mysqli = new MySQLi(
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
	// ensuring connection for lazy loading 
	//------------------------------------------------
	
	private function ensure_connection(){
		switch($this->state){
			case self::STATE_CONNECTED :
				return true;
			case self::STATE_UNCONNECTED :
				return $this->connect();
			case self::STATE_FAILED :
				return false;
			default:
				return false;
		}
	}
	
	//------------------------------------------------
	// query template 
	//------------------------------------------------
	
	private function _query($query){
		
		// ensuring connection
		if(!$this->ensure_connection()){
			return false;
		}
		
		// query
		$result = $this->mysqli->query($query);
		
		// error handling
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
	// backward compatibility; deprecated?
	//------------------------------------------------
	
	function query($query){
		
		// query
		$result = $this->_query($query);
		
		// boolean result
		if(is_bool($result)){
			return $result;
		}
		
		// rows in result
		$rows = [];
		while($row = $result->fetch_assoc()){
			$rows[] = $row;
		}
		
		// freeing result
		$result->free();
		
		return $rows;
	}
	
	//------------------------------------------------
	// executing query
	//------------------------------------------------
	// shouldn't it return number of rows affected?
	//------------------------------------------------
	
	function execute($query){
		
		// query
		$result = $this->_query($query);
		
		// returning result
		if($result === false) return false;
		
		return true;
	}
	
	//------------------------------------------------
	// returning one value
	//------------------------------------------------
	
	function fetch_value($query){
		
		// query
		$result = $this->_query($query);
		
		// failure handling
		if($result === false) return false;
		
		// returning first value of first row
		$row = $result->fetch_row();
		$value = $row[0];
		
		// freeing result
		$result->free();
		
		return $value;
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
		
		// freeing result
		$result->free();
		
		return $row;
		
	}
	
	function fetch_row($query){
		return $this->fetch_one($query);
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
		
		// freeing result
		$result->free();
		
		return $rows;
	}
	
	//------------------------------------------------
	// returning one column
	//------------------------------------------------
	
	function fetch_column($query){
		
		//query
		$result = $this->_query($query);
		
		// failure handling
		if($result === false) return false;
		
		// returning array of first values of every row
		$column = [];
		while($row = $result->fetch_row()){
			$column[] = $row[0];
		}
		
		// freeing result
		$result->free();
		
		return $column;
	}
	
	//------------------------------------------------
	// informative functions
	//------------------------------------------------
	
	function get_affected_rows(){
		return $this->mysqli->affected_rows;
	}
	
	function get_last_insert_id(){
		return $this->mysqli->insert_id;
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
		
		// ensuring connection
		if(!$this->ensure_connection()){
			return false;
		}
		
		$this->mysqli->autocommit(false);
	}
	
	function commit_transaction(){
		
		// ensuring connection
		if(!$this->ensure_connection()){
			return false;
		}
		
		$this->mysqli->commit();
		$this->mysqli->autocommit(true);
	}
	
	function rollback_transaction(){
		
		// ensuring connection
		if(!$this->ensure_connection()){
			return false;
		}
		
		$this->mysqli->rollback();
		$this->mysqli->autocommit(true);
	}
	
	//------------------------------------------------
	// string escaping
	//------------------------------------------------
	
	function escape_string($string){
		
		// ensuring connection
		if(!$this->ensure_connection()){
			return false;
		}
		// connection failed
		
		return $this->mysqli->real_escape_string($string);
	}
	
	//------------------------------------------------
	// closing connection
	//------------------------------------------------
	
	function close(){
		
		// ensuring connection
		if(!$this->ensure_connection()){
			return false;
		}
		
		$this->mysqli->close();
		
		return true;
	}
	
}

