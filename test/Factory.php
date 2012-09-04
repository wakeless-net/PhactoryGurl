<?php

class PhactoryGurl_Test_Factory extends PHPUnit_Framework_TestCase {
  function registerBasicBuilder() {
    PhactoryGurl_Definitions::register("TestOb", function() {
      return ["one", "two", "three"];
    });
  }

  function testBasicEndToEnd() {
    $this->registerBasicBuilder();

    $ob = PhactoryGurl_Factory::create("TestOb");

    $this->assertInstanceOf("TestOb", $ob);
    $this->assertEquals(["one", "two", "three"], $ob->arg);
  }

  function testMerge() {
    $this->registerBasicBuilder();

    $ob = PhactoryGurl_Factory::create("TestOb", ["four", "five"]);
    $this->assertEquals(["one", "two", "three", "four", "five"], $ob->arg);
  }

  function testBuildCallsSaver() {
    $this->fail("Need to change over to singleton I think");
  }

  function testComplexBuilder() {

    $this->registerBasicBuilder();
    PhactoryGurl_Factory::define_builder(function($class_name, $args) {
      return new TestProxy($class_name, $args);
    });


    $ob = PhactoryGurl_Factory::create("TestOb");

    $this->assertInstanceOf("TestProxy", $ob);
    $this->assertEquals("TestOb", $ob->class);
    $this->assertEquals(["one", "two", "three"], $ob->arg);
  }

}


class TestOb {
  var $arg;

  function __construct($args) {
    $this->arg = $args;
  }
}

class TestProxy {
  var $class;
  var $arg;

  function __construct($class, $arg) {
    $this->class = $class;
    $this->arg = $arg;
  }
}
