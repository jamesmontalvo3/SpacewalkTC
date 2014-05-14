<?php

class Model_Itemdefault extends RedBean_SimpleModel { 

	public function updateModel ($params) {

		// get the RedBeansPHP "bean" for this model
		$bean = $this->unbox();

		$bean->username           = $params['ims_cage'];
		$bean->ims_pn             = $params['ims_pn'];
		$bean->display_text       = $params['display_text'];
		$bean->allow_multiple_qty = $params['allow_multiple_qty'];
	
		return \R::store( $bean ); // returns ID
	}

}