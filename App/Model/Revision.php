<?php

class Model_Revision extends Redrevision_SimpleModel { 

	public function updateModel ($params) {

		//@TODO: Simplify this with proper Session handling...
		$user = \R::findOne( 'user', ' username = ? ', [ $_SERVER['REMOTE_ADDR'] ])

		// get the RedrevisionsPHP "bean" for this model
		$revision = $this->unbox();

		$revision->event_id    = $params['event_id'];
		$revision->jedi        = $params['jedi'];
		$revision->overview    = $params['overview'];
		$revision->revision_ts = date( "YmdHis", time() ); // revision saved
		$revision->items_json  = $params['items_json'];
		$revision->user_id     = $user->id; // @TODO: link this to the user model
		
		// userlast table unique on user ID and event ID
		$userlast = \R::findOne( 'userlast', ' e_id = ? AND u_id = ? ', [$revision->event_id, $user->id] )

		// ori_rev : get from `userlast` table
		$revision->userlast = $userlast;

		// version : not updated here. set when a revision is published.
	
		$new_rev_id = \R::store( $revision ); // returns ID of event

		$userlast->r_id = $new_rev_id;

		//@TODO: store use

		return null; //????????
	}

}