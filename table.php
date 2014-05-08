<?php

// TODO:
//  - decide on selection method
//  - drop() method

require_once __DIR__ . '/database.php';

class Table {
	
	protected $database;
	protected $table;
	
	function __construct(Database $database, $table){
		$this->database  = $database;
		$this->table     = $table;
	}
	
	//----------------------------------------
	// inserting
	//----------------------------------------
	
	function insert(Array $values){
		$query =
			"INSERT INTO `$this->table`" .
			' SET ' . $this->prepare_values($values) .
			';';
		$this->database->execute($query);
	}
	
	//----------------------------------------
	// replaceing
	//----------------------------------------
	
	function replace(Array $values){
		$query =
			"REPLACE INTO `$this->table`" .
			' SET ' . $this->prepare_values($values) .
			';';
		$this->database->execute($query);
	}
	
	//----------------------------------------
	// preparing values for query
	//----------------------------------------
	
	protected function prepare_values(Array $values){
		$pairs = [];
		foreach($values as $key => $value){
			$pairs[] = "`$key` = " . $this->prepare_value($value);
		}
		return implode(', ', $pairs);
	}
	
	//----------------------------------------
	// preparing value for query
	//----------------------------------------
	// how should the method behave in case
	// of untranslatable types like object?
	// map to null? ignore?
	
	protected function prepare_value($value){
		switch(gettype($value)){
			case 'integer':
			case 'double':
				return $value;
				break;
			case 'string':
				return "'$value'";
			default:
				return 'NULL';
		}
	}
	
}
