<?php

// TODO:
//  - decide on selection method
//  - drop() method

namespace Database;

require_once __DIR__ . '/database.php';

class Table {
	
	protected $database;
	protected $table;
	
	function __construct(\Database $database, $table){
		$this->database  = $database;
		$this->table     = $table;
	}
	
	//----------------------------------------
	// inserting
	//----------------------------------------
	
	function insert(array $values){
		$query =
			"INSERT INTO `$this->table`" .
			' SET ' . $this->prepare_values($values) .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//----------------------------------------
	// replaceing
	//----------------------------------------
	
	function replace(array $values){
		$query =
			"REPLACE INTO `$this->table`" .
			' SET ' . $this->prepare_values($values) .
			';';
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//----------------------------------------
	// dropping
	//----------------------------------------
	// "if exists" should be part of the drop() method:
	//   1) \Database\Table::IF_EXISTS
	//   2) Database\IF_EXISTS
	//   3) $table->drop()->execute();
	//      $table->drop()->if_exists()->execute();
	
	function drop(){
		$query = "DROP TABLE `$this->table`;";
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	function drop_if_exists(){
		$query = "DROP TABLE IF EXISTS `$this->table`;";
		$result = $this->database->execute($query);
		
		return $result;
	}
	
	//----------------------------------------
	// preparing values for query
	//----------------------------------------
	
	protected function prepare_values(array $values){
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
