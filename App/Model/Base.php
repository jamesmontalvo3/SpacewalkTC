<?php

namespace Spacewalk\Model;

class Base {

	protected $db;

	public function __construct () {
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

	public function store () {
		if ($this->id === null)
			return $this->insert();
		else
			return $this->update();
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

	public function toJSON () {
		$out = array();
		foreach($this->columns as $col) {
			$out[$col] = $this->$col;
		}
		return json_encode($out);
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