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
		$m = \R::findAll( 
			$this->table,
			" ORDER BY {$this->orderbycolumn} {$this->orderbydirection} LIMIT {$this->indexlimit} "
		);
		return json_encode( \R::exportAll( $m ) );
	}

	// read
	public function read ($id) {
		$m = \R::load( $this->table, $id );
		return json_encode( $m->export() );
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

	abstract protected function updateModel ($id, $params);

}