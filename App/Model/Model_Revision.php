<?php

class Model_Revision extends RedBean_SimpleModel { 

	public function updateModel ($params) {

		//@TODO: Simplify this with proper Session handling...
		$user = \R::findOne( 'user', ' username = ? ', [ $_SERVER['REMOTE_ADDR'] ])

		// get the RedBeansPHP "bean" for this model
		$bean = $this->unbox();

		$bean->event_id    = $params['event_id'];
		$bean->jedi        = $params['jedi'];
		$bean->overview    = $params['overview'];
		$bean->revision_ts = date( "YmdHis", time() ); // revision saved
		$bean->items_json  = $params['items_json'];
		$bean->u_id        = $user->id; // @TODO: link this to the user model
		
		// userlast table unique on user ID and event ID
		$userlast = \R::findOne( 'userlast', ' e_id = ? AND u_id = ? ', [$bean->event_id, $user->id] )

		// ori_rev : get from `userlast` table
		$bean->ori_rev = $userlast->r_id;

		// version : not updated here. set when a revision is published.
	
		$new_rev_id = \R::store( $bean ); // returns ID of event

		$userlast->r_id = $new_rev_id;

		//@TODO: store use

		return null; //????????
	}

}