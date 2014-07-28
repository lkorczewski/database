<?php

require_once __DIR__ . '/../test_cases/extended_database_test_case.php';

use Database\Extended_Database;

class Insert_Test extends Extended_Database_Test_Case {
	
	protected $database;
	protected $insert_table;
	
	function setup(){
		parent::setup();
		
		$query =
			'CREATE TABLE insert_test (' .
			' id INT AUTO_INCREMENT PRIMARY KEY,' .
			' field_1 INT,' .
			' field_2 VARCHAR(32)' .
			')' .
			';';
		$result = $this->database->execute($query);
		
		$this->table = $this->database->get_table('insert_test');
		
	}
	
	function test_inserting_new_record(){
		
		$written_record = [
			'field_1'  => 1,
			'field_2'  => 'value_2',
		];

		$this->table
			->insert($written_record);

		$query =
			'SELECT *' .
			' FROM insert_test' .
			' WHERE field_1 = 1' .
			';';
		$read_record = $this->database->fetch_one($query);

		unset($read_record['id']);
		
		$this->assertFalse($this->database->get_last_error());
		$this->assertEquals($read_record, $written_record);
	}
	
	function test_inserting_existing_record(){
		
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
		$this->table->insert($replacing_record);
		
		$query =
			'SELECT *' .
			' FROM insert_test' .
			' WHERE id = 1' .
			';';
		$read_record = $this->database->fetch_one($query);
		
		$this->assertNotEmpty($this->database->get_last_error());
		$this->assertEquals($read_record, $initial_record);
	}
	
	// TODO: test_inserting_unmatching record
	
	function teardown(){
		parent::teardown();
		
		$this->table->drop_if_exists();
	}
	
}
