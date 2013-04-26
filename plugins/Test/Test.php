<?php

class Test {
	
	public function get_information() {
		return array(
		    'name' => 'Test Plugin',
		    'description' => 'This is a test description',
		    'author' => 'hice3000',
		    'version' => 0
		);
	}
	
	public function register_hooks() {
		return array(
		    'system.pre_controller' => 'pre_controller'
		);
	}
	
	public function pre_controller($params) {
		// If you do not want to change something in the params,
		// you do not have to return it.
	}
	 
}

?>
