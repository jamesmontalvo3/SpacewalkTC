<?php

namespace Spacewalk\Model;

/*
get full model
get json
 */
abstract class Model {

	protected $db;

	// models must define these
	protected $table;
	protected $columns;

	// defaults: models should override as required
	protected $indexlimit = 10;
	protected $orderbycolumn = 'id';
	protected $orderbydirection = 'DESC';


	public function __construct ($table) {
		$this->table = $table;
		$this->db = $this->getDB();
	}

	protected function getDB () {

		global $swalk_dbstring, $swalk_dbuser, $swalk_dbpass;
		try {
			$DBH = new PDO($swalk_dbstring, $swalk_dbuser, $swalk_dbpass);
			// $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
			// $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch(PDOException $e) {
		    echo $e->getMessage();
		}
		return $DBH;

	}

	public function loadParams ( $slim_request ) {
		foreach($this->columns as $col) {
			if ($slim_request->param( $col ) ) {
				$this->$col = $slim_request->param( $col );
			}
		}
		return $this;
	}

	public function save () {
		if ($this->id === null)
			return $this->insertDB();
		else
			return $this->updateDB();
	}

	protected function insertDB () {

		$valued_cols = array();
		$col_data = array();
		foreach($this->columns as $col) {
			if ($this->$col !== null) {
				$valued_cols[] = $col;
				$col_data[$col] = $this->$col;
			}
		}
		$STH = $this->db->prepare(
			"INSERT INTO {$this->table} (`" . implode('`,`', $valued_cols) . "`) VALUES (:". implode(', :', $valued_cols) .")"
		);
		$STH->execute($col_data);

		return $this->db->lastInsertId();
	}

	protected function updateDB () {

		$query_helper = array();
		$col_data = array();
		foreach($this->columns as $col) {
			if ($this->$col !== null) {
				$query_helper[] = "`$col` = :$col";
				$col_data[$col] = $this->$col;
			}
		}
		$STH = $this->db->prepare(
			"UPDATE {$this->table} SET " . implode(',', $query_helper) . " WHERE id = :id"
		);
		$col_data['id'] = $this->col;
		$STH->execute($col_data);

		return $this->id;
	
	}

	public function getAsArray () {
		$out = array();
		foreach($this->columns as $col) {
			$out[$col] = $this->$col;
		}
		return $out;
	}

	public function toJSON () {
		return json_encode( $this->getAsArray() ); // @todo: isn't this parent functionality?
	}



	public function load ($id) {
		$STH = $this->db->query("SELECT * FROM {$this->table} LIMIT 1");
		$STH->setFetchMode(\PDO::FETCH_CLASS, );
		
		$row = $STH->fetch();
		return $row;
	}

	public function selectAll() {

		$STH = $this->db->query(
			"SELECT * FROM {$this->table} 
			ORDER BY {$this->orderbycolumn} {$this->orderbydirection} 
			LIMIT {$this->indexlimit}"
		);
		$STH->setFetchMode(\PDO::FETCH_ASSOC);

		$out = array();
		while($row = $STH->fetch()) {
			$out[] = $row;
		}
		return $out;

	}


	public function update ($params) {



		$bean->name = $params['name'];
		$bean->datetime = $params['datetime'];
		
		return \R::store( $bean ); // returns ID of event
	}

}