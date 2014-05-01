<?php

class Model_User extends RedBean_SimpleModel { 

	public function updateModel ($params) {

		// get the RedBeansPHP "bean" for this model
		$bean = $this->unbox();

		$bean->username    = $params['username'];
		$bean->perm_groups = $params['perm_groups'];
	
		return \R::store( $bean ); // returns ID
	}

}