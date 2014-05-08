<?php

// TODO:
//  - table creation interface

namespace Database;

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/table.php';

class Extended_Database extends \Database {
	
	function get_table($table){
		return new Table($this, $table);
	}
	
}
