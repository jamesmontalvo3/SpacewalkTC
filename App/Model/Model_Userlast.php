<?php

class Model_Userlast extends RedBean_SimpleModel { 

	public function updateModel ($params) {

		// get the RedBeansPHP "bean" for this model
		$bean = $this->unbox();

		$bean->u_id = $params['u_id'];
		$bean->e_id = $params['e_id'];
		$bean->r_id = $params['r_id'];

		return \R::store( $bean ); // returns ID
	}

}