<?php

namespace Spacewalk\Controller;

abstract class Base {

	protected $model;
	protected $table;
	protected $indexlimit = 10;
	protected $orderbycolumn = 'id';
	protected $orderbydirection = 'DESC';


	public function __construct ($model, $table) {
		$this->model = $model;
		$this->table = $table;
	}

	// index
	public function index () {
		return json_encode( $this->model->selectAll() );
	}

	// read
	public function read ($id) {
		return json_encode( $this->model->load($id) );
	}

	// create
	public function create ($params) {
		return $this->updateModel(
			\R::dispense( $this->table ),
			$params
		);
	}

	// update
	public function update ($id, $params) {
		return \R::load( $this->table, $id ) // load RedBeansPHP "bean" by table and ID
			->box()  // returns the model associated with this "bean" in /App/Model/
			->updateModel($params);
	}

	// delete
	public function delete ($id) {
		\R::trash( \R::load( $this->table, $id ) );
	}

	// abstract protected function updateModel ($id, $params);

}