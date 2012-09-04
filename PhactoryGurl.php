<?php


class PhactoryGurl_Definitions {
  static protected $builders = [];
  static function register($class_name, $builder) {
    self::$builders[$class_name] = $builder;
  }

  static function registered($class_name) {
    return isset(self::$builders[$class_name]);
  }

  static function builder($class_name) {
    if(!self::registered($class_name)) throw new Exception("$class_name has not been registered to PhactoryGurl.");
    return self::$builders[$class_name];
  }
}

class PhactoryGurl_Factory {

  static protected $builder = null;


  static function define_builder($func) {
    self::$builder = $func;
  }

  function build($class, $args = []) {
    $ob = self::create($class, $args);
    return self::saver($ob);
  }

  static function create($class, $args = []) {
    $argFunc = PhactoryGurl_Definitions::builder($class);

    return self::builder($class, self::merge($argFunc(), $args));
  }


  static function merge($first_args, $second_args) {
    return array_merge($first_args, $second_args);
  }

  static function saver($object) {
    $object->save();
    return $object;
  }

  static function builder($class, $array) {
    if(!self::$builder) {
      return new $class($array);
    } else {
      $func = self::$builder;
      return $func($class, $array);
    }
  }

}

