<?php

class PhactoryGurl_Dataset extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet {

  function __construct($tables) {
    $this->tables = $tables;
  }

  function getTableNames() {
    return $this->tables;
  }

  public function getTableMetaData($tableName) {
      return new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, array(), array());
  }

  function createIterator($reverse = false) {
    $tables = array();
    foreach($this->tables as $table) {
      $tables[] = new PHPUnit_Extensions_Database_DataSet_DefaultTable($this->getTableMetaData($table));
    }
    return new PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($tables, $reverse);
  }
}


class EmptyTable extends PHPUnit_Extensions_Database_DataSet_AbstractTable {
  function __construct() {
    $this->data = array();
  }
}

