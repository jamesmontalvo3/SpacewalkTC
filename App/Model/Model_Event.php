<?php

class Model_Event extends RedBean_SimpleModel { 
// class Model_Event extends \RedBeanPHP\SimpleModel { 
	// public function update() {
	// 	if (strlen($this->password) < 8) {
	// 		throw new Exception('Password must be at least 8 characters long');
	// 	}
	// }

	// public function open() {
	// 	echo "test";
	// }


	public function updateModel ($params) {

		// get the RedBeansPHP "bean" for this model
		$bean = $this->unbox();

		$bean->name = $params['name'];
		$bean->datetime = $params['datetime'];
		
		return \R::store( $bean ); // returns ID of event
	}

}