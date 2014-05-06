<?php

namespace Spacewalk\Model;
require_once "App/Model/Model.php";

class Event extends Model { 

	// database columns (id assumed)
	protected $columns = array(
		"name",
		"released_rev_id",
		"status"
	);

	public function __construct ($id) {
		parent::__construct("event");
		$this->id = $id
	}


	// handled by base Model
	// public function load ($id) {}

	// public function loadParams ( $slim_request ) {
	// 	parent::loadParams( $slim_request );


	// 	$this->newRevision()->loadParams( $slim_request );
	// }

	public function newRevision () {

		if ( ! isset($this->revisions) )
			$this->revisions = array();

		return $this->revisions[] = new Revision();
	}


	public function getAsArray () {

		$ev = parent::getAsArray();

		if ($this->released_rev) {
			$ev['released_rev'] = $this->released_rev->getAsArray();
		}
		if ($this->latest_rev) {
			$ev['latest_rev'] = $this->latest_rev->getAsArray();
		}
		if (is_array($this->revisions) && count($this->revisions) > 0 ) {
			$ev['revisions'] = array();
			foreach($this->revisions as $rev) {
				$ev['revisions'][] = $rev->getAsArray();
			}
		}

		return $ev;
	}


}


/*
revision

	id
	event_id
	version
	jedi
	overview
	ori_rev_id
	revision_ts
	user_id
	items_json


	 * @querystring:
	 *     eventinfo = false        => optional, default true, returns rev info only
	 *     revision = multiple pipe separated
	 *         latest      => most recent rev, whether official or draft
	 *         published   => get the "official" rev
	 *         <int>       => get rev with id = <int>
	 *         history     => all revs
	 *             history_order   => ASC or DESC (default DESC, most recent)
	 *             history_limit   => <int> (default 10)
	 *             history_before_id => <rev ID> (default null) finds revs prior to <rev ID>
	 *             history_after_id  => <rev ID> (default null) finds revs after <rev ID>
	 *             history_before_ts => <yyyymmddhhmmss> (default null) finds rev after timestamp
	 *             history_after_ts  => <yyyymmddhhmmss> (default null) finds rev after timestamp
	 *         new         => all revs since published



	public function updateModel ($params) {

		// get the RedBeansPHP "bean" for this model
		$bean = $this->unbox();

		$bean->name = $params['name'];
		$bean->datetime = $params['datetime'];
		
		return \R::store( $bean ); // returns ID of event
	}

*/