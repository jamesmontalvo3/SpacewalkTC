<?php

namespace Spacewalk\Controller;
require_once "App/Controller/Base.php";

class Revision extends Base {

	// @TODO: update these when figured out RedBeans created_at and such...
	//protected $indexlimit = 10; // default 10
	//protected $orderbycolumn = ''; // default 'id'
	//protected $orderbydirection = 'DESC'; // default 'DESC'

	public function __construct () {
		parent::__construct(null, "revision");
	}

	// index  : use Base::index()
	// read   : use Base::read($id);
	// create : use Base::create($params);
	// update : use Base::update($id, $params);
	// delete : use Base::delete($id);

	// @TODO: this MUST be in a model class
	// protected function updateModel ($model, $params) {
	// 	$model->name = $params['name'];
	// 	$model->datetime = $params['datetime'];
	// 	return \R::store( $model ); // returns ID of event
	// }

}