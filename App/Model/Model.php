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
		$this->db = self::getDB();
	}

	static protected function getDB () {

		global $swalk_dbstring, $swalk_dbuser, $swalk_dbpass;
		try {
			$DBH = new \PDO($swalk_dbstring, $swalk_dbuser, $swalk_dbpass);
			// $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
			// $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$DBH->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			$DBH->setAttribute( \PDO::ATTR_EMULATE_PREPARES, false );
		}
		catch(PDOException $e) {
		    echo $e->getMessage();
		}
		return $DBH;

	}

	public function loadParams ( $slim_request ) {
		foreach($this->columns as $col) {
			if ( $slim_request->params( $col ) ) {
				$this->$col = $slim_request->params( $col );
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
			if (isset($this->$col) && $this->$col !== null) {
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
			if (isset($this->$col)) {
				$query_helper[] = "`$col` = :$col";
				$col_data[':'.$col] = $this->$col;
			}
		}
		if (count($query_helper) > 0) {
			$query = "UPDATE {$this->table} SET " . implode(',', $query_helper) . " WHERE id = :id";
			$STH = $this->db->prepare( $query );
			$col_data[':id'] = $this->id;
			$STH->execute($col_data);
		}
		return $this->id;
	
	}

	public function set ($params) {
		foreach($this->columns as $col) {
			if ( isset($params[$col]) ) {
				$this->$col = $params[$col];
			}
		}
		if ( isset($params['id']) )
			$this->id = $params['id'];

		return $this;
	}

	public function getAsArray () {
		$out = array('id' => $this->id);
		foreach($this->columns as $col) {
			if ( isset($this->$col) )
				$out[$col] = $this->$col;
			else
				$out[$col] = null;
		}
		return $out;
	}

	public function toJSON () {
		return "<pre>" . json_encode( $this->getAsArray(), JSON_PRETTY_PRINT ) . "</pre>"; // @todo: isn't this parent functionality?
		// return json_encode( $this->getAsArray() ); // @todo: isn't this parent functionality?
	}



	public function load ($id=null) {
		if ($id && intval($id))
			$this->id = intval($id);

		$STH = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
		$STH->execute(array(":id" => $this->id));
		$STH->setFetchMode(\PDO::FETCH_ASSOC); //, "Spacewalk\\Model\\" . ucfirst($this->table));

		$row = $STH->fetch();
		// echo "test";print_r($row);exit();
		foreach($row as $col => $val) {
			$this->$col = $val;
		}
		return $this;
	}

	public function selectAll() {

		$STH = self::getDB()->query(
			"SELECT * FROM {$this->table} 
			ORDER BY {$this->orderbycolumn} {$this->orderbydirection} 
			LIMIT {$this->indexlimit}"
		);
		$STH->setFetchMode(\PDO::FETCH_ASSOC);

		// $out = array();
		// while($row = $STH->fetch()) {
		// 	$out[] = $row;
		// }
		// echo "<pre>";
		// print_r( $STH->fetchAll() );
		// echo "</pre>";
		return $STH->fetchAll();

	}


	// public function update ($params) {

	// 	$bean->name = $params['name'];
	// 	$bean->datetime = $params['datetime'];
		
	// 	return \R::store( $bean ); // returns ID of event
	// }

}