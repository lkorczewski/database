<?php

//====================================================
// Łukasz Korczewski
// database class
//====================================================

class Database {
	
	private $mysqli;
	private $state;
	
	private $host;
	private $port;
	private $user;
	private $password;
	private $database;
	
	function __construct(){
		$this->state = 0;
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
		
		$this->mysqli = mysqli_connect(
			$this->host,
			$this->user,
			$this->password,
			$this->database,
			$this->port
		);
		
		if(mysqli_connect_errno()) {
			$error = mysqli_connect_error();
			echo "<p style=\"color: red\">$error</p>";
			return false;
		}
		
		mysqli_set_charset($this->mysqli, 'UTF8');
		
		$this->state = 1;
		return true;
	}

	//------------------------------------------------
	// executing query
	//------------------------------------------------
	
	function query($query){
		
		// connection not initialized
		if($this->state == 0) {
			$this->connect();
		}
		
		// query
		$result = mysqli_query($this->mysqli, $query);
		
		// boolean result
		if(!mysqli_num_rows($result))
			return $result;
		
		// rows in result
		$array_result = array();
		while($row = mysqli_fetch_assoc($result)){
			$array_result[] = $row;
		}
		return $array_result;
	}
	
	//------------------------------------------------
	// closing connection
	//------------------------------------------------
	
	function close(){
		mysqli_close($this->mysqli);
	}
	
}

?>
