<?php

namespace Spacewalk\Model;
require_once "App/Model/Base.php";

class Event extends Base { 

	// database columns (id assumed)
	protected $columns = array(
		"datetime",
		"name",
		"released_rev_id"
	);

	public function __construct () {

	}



	public function updateModel ($params) {

		// get the RedBeansPHP "bean" for this model
		$bean = $this->unbox();

		$bean->name = $params['name'];
		$bean->datetime = $params['datetime'];
		
		return \R::store( $bean ); // returns ID of event
	}

}