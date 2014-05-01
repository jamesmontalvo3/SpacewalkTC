<?php

namespace Spacewalk\Controller;
require_once "App/Controller/Base.php";

class Event extends Base {

	protected $indexlimit = 10; // default 10
	protected $orderbycolumn = 'datetime'; // default 'id'
	protected $orderbydirection = 'DESC'; // default 'DESC'

	public function __construct () {
		
		parent::__construct(null, "event");
	}

	// index  : use Base::index()
	// read   : use Base::read($id);
	// create : use Base::create($params);
	// update : use Base::update($id, $params);
	// delete : use Base::delete($id);

	// @TODO: this MUST be in a model class
	// protected function updateModel ($eventORM, $params) {
	// 	// $eventORM->name = $params['name'];
	// 	// $eventORM->datetime = $params['datetime'];
	// 	// return \R::store( $eventORM ); // returns ID of event

	// 	return $eventORM->box()->updateModel($params);

	// }

}