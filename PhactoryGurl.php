<?php

require_once "PhactoryGurl/Dataset.php";


trait PhactoryGurl {
  protected $factory;

  function setUp() {
    parent::setUp();

    if(true) { //Check if this is a database test case
    } else {
      //otherwise instantiate our own database test case functionality.
    }
  }

  function getDataSet() {
    return $this->factory()->emptyDataset();
  } 

  function build($class, $args = array()) {
    return $this->factory()->build($class, $args);
  }

  function buildOrFind($class, $args= array()) {
    return $this->factory()->buildOrFind($class, $args);
  }

  function create($class, $args = array()) {
    return $this->factory()->create($class, $args);
  }

  function mock($class, $args = array()) {
    throw new Exception("Not implemented.");
  }

  function setFactory($factory) {
    $this->factory = $factory;
  }

  function factory() {
    if($this->factory) {
      return $this->factory;
    } else {
      return $this->factory = new PhactoryGurl_Factory;
    }
  }


}

class PhactoryGurl_Definitions {
  static protected $builders = [];
  static protected $attributes = [];

  static function clear() {
    self::$builders = array();
    self::$attributes = array();
  }

  static function register($class_name, $attributes, $builder = null) {
    if(is_null($builder)) {
      $builder = $attributes;
      $attributes = array();
    }
    

    self::$attributes[$class_name] = $attributes;
    self::$builders[$class_name] = $builder;
  }

  static function registered($class_name) {
    return isset(self::$builders[$class_name]);
  }

  static function definition($gurl) {
    if(!self::registered($gurl)) throw new Exception("$gurl has not been registered to PhactoryGurl.");


    return array("builder" => self::$builders[$gurl]) + array_merge(["class" => $gurl], self::$attributes[$gurl] ?: array());
  }

  static function builder($gurl) {
    if(!self::registered($gurl)) throw new Exception("$gurl has not been registered to PhactoryGurl.");
    return self::$builders[$gurl];
  }

  static function tables() {
    return array_map(function($i) { return $i["table"]; }, self::$attributes);
  }
}

abstract class PhactoryGurl_Adapter {
  abstract function save($object);
  abstract function create($class, $args);
}

class PhactoryGurl_Default_Adapter  extends PhactoryGurl_Adapter {
  function save($object) {
    $object->save();
    return $object;
  }


  function create($class, $args) {
    return new $class($args);
  }

}

class PhactoryGurl_Factory {
  public static $factory;
  

  static protected $adapter = null;


  static function setAdapter($adapter) {
    self::$adapter = $adapter;
  }

  static function adapter() {
    if(self::$adapter) {
      return self::$adapter;
    } else {
      return self::$adapter = new PhactoryGurl_Default_Adapter;
    }
  }


  function emptyDataset() {
    return new PhactoryGurl_Dataset(PhactoryGurl_Definitions::tables());
  }


  function getDefinition($gurl) {
    return $definition = PhactoryGurl_Definitions::definition($gurl);
  }


  function create($gurl, $args = array()) {
    $definition = $this->getDefinition($gurl);

    $class = $definition["class"];

    return $this->adapter()->create($class, $this->getArgs($gurl, $args));
  }


  function build($class, $args = array()) {
    $ob = $this->create($class, $args);
    return $this->save($ob);
  }


  function save($object) {
    return $this->adapter()->save($object);
  }


  protected function getArgs($class, $args) {
    $argFunc = PhactoryGurl_Definitions::builder($class);

    return self::merge($argFunc($this), $args);
  }


  static function merge($first_args, $second_args) {
    return array_merge($first_args ?: array(), $second_args ?: array());
  }

  static function __callStatic($func, $args) {
    return call_user_func_array("self::$func", $args);
  }

}

