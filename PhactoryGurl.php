<?php


trait PhactoryGurl {
  function getDataSet() {
    return array();
  } 

  function build($class, $args = array()) {
    return static::factory()->build($class, $args);
  }

  static function setFactory($factory) {
    PhactoryGurl_Factory::$factory = $factory;
  }

  static function factory() {
    if(PhactoryGurl_Factory::$factory) {
      return PhactoryGurl_Factory::$factory;
    } else {
      return PhactoryGurl_Factory::$factory = new PhactoryGurl_Factory;
    }
  }


}

class PhactoryGurl_Definitions {
  static protected $builders = [];
  static function register($class_name, $builder) {
    self::$builders[$class_name] = $builder;
  }

  static function registered($class_name) {
    return isset(self::$builders[$class_name]);
  }

  static function definition($class_name) {
    if(!self::registered($class_name)) throw new Exception("$class_name has not been registered to PhactoryGurl.");
    return self::$builders[$class_name];
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

  function build($class, $args = array()) {
    $ob = $this->create($class, $args);
    return $this->save($ob);
  }

  function save($object) {
    return $this->adapter()->save($object);
  }

  protected function getArgs($class, $args) {
    $argFunc = PhactoryGurl_Definitions::definition($class);
    return self::merge($argFunc(), $args);
  }

  function create($class, $args = array()) {
    return $this->adapter()->create($class, $this->getArgs($class, $args));
  }


  static function merge($first_args, $second_args) {
    return array_merge($first_args, $second_args);
  }

  static function __callStatic($func, $args) {
    return call_user_func_array("self::$func", $args);
  }

}

