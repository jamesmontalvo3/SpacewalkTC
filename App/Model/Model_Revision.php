<?php

class Model_Revision extends RedBean_SimpleModel { 
// class Model_Event extends \RedBeanPHP\SimpleModel { 
	// public function update() {
	// 	if (strlen($this->password) < 8) {
	// 		throw new Exception('Password must be at least 8 characters long');
	// 	}
	// }


	public function updateModel ($params) {

		// get the RedBeansPHP "bean" for this model
		$bean = $this->unbox();

		$bean->event_id    = $params['event_id'];
		$bean->jedi        = $params['jedi'];
		$bean->overview    = $params['overview'];
		$bean->revision_ts = date( "YmdHis", time() ); // revision saved
		$bean->items_json  = $params['items_json'];
		$bean->username    = $_SERVER['REMOTE_ADDR']; // @TODO: link this to the user model
		// ori_rev : get from `userlast` table
		
		// version : not updated here. set when a revision is published.

	
		return \R::store( $bean ); // returns ID of event
	}

}