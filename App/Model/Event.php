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

	public function __construct ($id=null) {
		parent::__construct("event");
		$this->id = $id;
		$this->revisions = array();
	}


	// handled by base Model
	// public function load ($id) {}

	// public function loadParams ( $slim_request ) {
	// 	parent::loadParams( $slim_request );


	// 	$this->newRevision()->loadParams( $slim_request );
	// }




	public function getLatestRev () {
		// require_once "/App/Model/Revision.php";

		// use Spacewalk\Model;

		$STH = $this->db->prepare(
			"SELECT * FROM revision WHERE event_id = :event_id ORDER BY revision_ts DESC LIMIT 1"
		);
		$STH->setFetchMode(\PDO::FETCH_ASSOC); //, 'Spacewalk\Model\Revision');
		$STH->execute(array( ':event_id' => $this->id ));

		$this->revisions[] = $STH->fetch();

	}

	public function getCurrentRev () {
		// use Spacewalk\Model\Revision;

		if ( isset($this->released_rev_id) && $this->released_rev_id ) {

			$STH = $this->db->prepare(
				"SELECT * FROM revision WHERE event_id = :event_id AND id = :rev_id LIMIT 1"
			);
			$STH->setFetchMode(\PDO::FETCH_ASSOC); //, 'Spacewalk\Model\Revision');
			$STH->execute(array( 
				':event_id' => $this->id,
				':rev_id' => $this->released_rev_id
			));

			return $this->newRevision()->set($STH->fetch());
		}
		else
			return null;
		// else {
		// 	$this->revisions[] = $this->newRevision();
		// }
	}

	public function getAllReleasedRevs () {

		// use Spacewalk\Model\Revision;

		// arbitrary limit 100, not likely to ever have that many versions
		$STH = $this->db->prepare(
			"SELECT * FROM revision WHERE event_id = :event_id AND version IS NOT NULL ORDER BY version DESC LIMIT 100"
		);
		$STH->setFetchMode(\PDO::FETCH_ASSOC); //, 'Spacewalk\Model\Revision');
		$STH->execute(array( ':event_id' => $this->id ));

		$this->revisions = $STH->fetchAll();

	}

	public function getNewRevs () {
		
		$current_ts = $this->getCurrentRev()->revision_ts;

		// use Spacewalk\Model\Revision;

		// arbitrary limit 5000...if we think there are going to be a lot of revisions
		// there should probably be pagination built in here...or this only shows ~100
		// revs and then the client side knows to use the 'history' action after that.
		$STH = $this->db->prepare(
			"SELECT * FROM revision WHERE event_id = :event_id AND revision_ts > :current_ts ORDER BY revision_ts DESC LIMIT 5000"
		);
		$STH->setFetchMode(\PDO::FETCH_ASSOC); //, 'Spacewalk\Model\Revision');
		$STH->execute(array(
			':event_id' => $this->id,
			':current_ts' => $current_ts
		));

		$this->revisions = $STH->fetchAll(); // clear the released version out
	}

	// @params: 'order','limit','before_id','after_id','before_ts','after_ts'
	/* $hist_params:
	 * order     => ASC or DESC (default DESC, most recent)
	 * limit     => <int> (default 10)
	 * before_id => <rev ID> (default null) finds revs prior to <rev ID>
	 * after_id  => <rev ID> (default null) finds revs after <rev ID>
	 * before_ts => <yyyymmddhhmmss> (default null) finds rev after timestamp
	 * after_ts  => <yyyymmddhhmmss> (default null) finds rev after timestamp    */
	public function getRevHistory( $hist_params ) {

		// use Spacewalk\Model\Revision;

		$query = "SELECT * FROM revision WHERE event_id = :event_id ";

		$hist_paginate_params = array(
			'before_id' => 'id < :before_id',
			'after_id'  => 'id > :after_id',
			'before_ts' => 'revision_ts < :before_ts',
			'after_ts'  => 'revision_ts > :after_ts'
		);

		$bind_params = array(':event_id' => $this->id);

		foreach($hist_paginate_params as $param => $query_string) {
			if ( isset($hist_params[$param]) ) {
				$query .= ' AND ' . $query_string;
				$bind_params[':'.$param] = $hist_params[$param];
			}
		}

		if ( isset($hist_params['order']) && strtolower($hist_params['order']) == 'asc' )
			$query .= ' ORDER BY revision_ts ASC';
		else
			$query .= ' ORDER BY revision_ts DESC';

		if ( isset($hist_params['limit']) && intval($hist_params['limit']) )
			$query .= ' LIMIT ' . intval($hist_params['limit']);
		else
			$query .= ' LIMIT 20';

		$STH = $this->db->prepare( $query );
		$STH->setFetchMode(\PDO::FETCH_ASSOC); //, 'Spacewalk\Model\Revision');
		$STH->execute($bind_params);

		$this->revisions[] = $STH->fetchAll();

	}

	public function addRevisions ( $rev_ids_requested ) {
		
		// convert IDs to integer values...not sure if this is required
		$rev_ids_requested = array_map(function($value){
		    return intval($value);
		}, $rev_ids_requested);

		$rev_ids = array_fill(0, count($rev_ids_requested), 'id = ?');
		$rev_ids = implode(' OR ', $rev_ids);
		// use Spacewalk\Model\Revision;

		array_unshift($rev_ids_requested, $this->id);
		
		// arbitrary limit 100, not likely to ever have that many versions
		$STH = $this->db->prepare(
			"SELECT * FROM revision WHERE event_id = ? AND ($rev_ids) ORDER BY revision_ts DESC LIMIT 100"
		);
		$STH->setFetchMode(\PDO::FETCH_ASSOC); //, 'Spacewalk\Model\Revision');
		$STH->execute($rev_ids_requested);

		$this->revisions[] = $STH->fetchAll();

	}


	public function newRevision ($revid=null) {

		if ( ! isset($this->revisions) )
			$this->revisions = array();
		require_once "App/Model/Revision.php";
		$rev = new Revision();
		$rev->event_id = $this->id;
		if ($revid)
			$rev->id = $revid;
		return $this->revisions[] = $rev;
	}

	public function getNextVersion () {
		$STH = $this->db->prepare(
			"SELECT version FROM revision WHERE event_id = :event_id AND version IS NOT NULL ORDER BY version DESC LIMIT 1"
		);
		$STH->setFetchMode(\PDO::FETCH_ASSOC); //, 'Spacewalk\Model\Revision');
		$STH->execute(array(':event_id'=>$this->id));
		$row = $STH->fetch();
		if ($row)
			return intval($row['version']) + 1;
		else
			return 1;
	}

	public function unreleaseRevision ($rev_id) {
		$current = $this->getCurrentRev();
		if ($current->id == $rev_id) {
			$STH = $this->db->prepare( "UPDATE {$this->table} SET released_rev_id = NULL WHERE id = :event_id" );
			$STH->execute(array(':event_id'=>$this->id));

			$STH = $this->db->prepare( "UPDATE {$current->table} SET version = NULL WHERE id = :rev_id" );
			$STH->execute(array(':rev_id'=>$current->id));
		}
	}


	public function getAsArray () {

		$ev = parent::getAsArray();

		// if ($this->released_rev) {
		// 	$ev['released_rev'] = $this->released_rev->getAsArray();
		// }
		// if ($this->latest_rev) {
		// 	$ev['latest_rev'] = $this->latest_rev->getAsArray();
		// }
		if (is_array($this->revisions) && count($this->revisions) > 0 ) {
			$ev['revisions'] = array();
			foreach($this->revisions as $rev) {
				if ( is_array($rev) )
					$ev['revisions'][] = $rev;
				else
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