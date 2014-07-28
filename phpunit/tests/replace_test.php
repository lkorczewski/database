<?php

require_once __DIR__ . '/../test_cases/extended_database_test_case.php';

use Database\Extended_Database;

class Replace_Test extends Extended_Database_Test_Case {
	
	protected $database;
	protected $insert_table;
	
	function setup(){
		parent::setup();
		
		$query =
			'CREATE TABLE replace_test (' .
			' id INT AUTO_INCREMENT PRIMARY KEY,' .
			' field_1 INT,' .
			' field_2 VARCHAR(32)' .
			')' .
			';';
		$result = $this->database->execute($query);
		
		$this->table = $this->database->get_table('replace_test');
		
	}
	
	function test_replacing_nonexistent_record(){
		
		$written_record = [
			'id'       => 1,
			'field_1'  => 1,
			'field_2'  => 'new_value_2',
		];

		$this->table
			->replace($written_record);

		$query =
			'SELECT *' .
			' FROM replace_test' .
			' WHERE id = 1' .
			';';
		$read_record = $this->database->fetch_one($query);
		
		$this->assertFalse($this->database->get_last_error());
		$this->assertEquals($written_record, $read_record);
	}
	
	function test_replacing_existent_record(){
		
		$initial_record = [
			'id'       => 1,
			'field_1'  => 1,
			'field_2'  => 'value_2',
		];
		$this->table->insert($initial_record);
		
		$replacing_record = [
			'id'       => 1,
			'field_1'  => 2,
			'field_2'  => 'new_value_2',
		];
		$this->table->replace($replacing_record);
		
		$query =
			'SELECT *' .
			' FROM replace_test' .
			' WHERE id = 1' .
			';';
		$read_record = $this->database->fetch_one($query);
		
		$this->assertFalse($this->database->get_last_error());
		$this->assertEquals($read_record, $replacing_record);
	}
	
	// TODO: test_inserting_unmatching record
	
	function teardown(){
		parent::teardown();
		
		$this->table->drop_if_exists();
	}
	
}
