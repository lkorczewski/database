<?php

use Database\Extended_Database;

abstract class Extended_Database_Test_Case extends PHPUnit_Framework_TestCase {
	
	protected $database;
	
	function setup(){
		$this->database = new Extended_Database([
			'user'      => 'test',
			'password'  => 'test',
			'database'  => 'test',
		]);
	}
	
	function teardown(){
	}
	
}
