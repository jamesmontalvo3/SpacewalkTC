<?php

namespace Spacewalk\Model;
require_once "App/Model/Model.php";

class revision extends Model { 

	// database columns (id assumed)
	protected $columns = array(
		'event_id',
		'gmt_date',
		'version',
		'jedi',
		'overview',
		'ori_rev_id',
		'revision_ts',
		'user_id',
		'items_json'
	);

	public function __construct ($id=null) {
		parent::__construct("revision");
		$this->id = $id;
	}

	public function save () {
		$this->event_id = ???;
		$this->ori_rev_id = ???;
		$this->revision_ts = ???;
		$this->user_id = ???;
		parent::save();
	}


	/*
	public function updateModel ($params) {

		//@TODO: Simplify this with proper Session handling...
		$user = \R::findOne( 'user', ' username = ? ', [ $_SERVER['REMOTE_ADDR'] ]);

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
	}*/

	// public function getAsArray () {
	// 	return array(
	// 		"id"          => $this->id,
	// 		"version"     => $this->version,
	// 		"jedi"        => $this->jedi,
	// 		"overview"    => $this->overview,
	// 		"ori_rev_id"  => $this->ori_rev_id,
	// 		"revision_ts" => $this->revision_ts,
	// 		"user_id"     => $this->user_id, // @todo: should this be more user info?
	// 		"items_json"  => $this->items_json
	// 	);
	// }

	public function toJSON () {
		return json_encode( $this->getAsArray() ); // @todo: isn't this parent functionality?
	}

}