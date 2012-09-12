<?php

class PhactoryGurl_FactoryTest extends PHPUnit_Framework_TestCase {
  function registerBasicBuilder() {
    PhactoryGurl_Definitions::register("TestOb", function() {
      return ["one", "two", "three"];
    });
  }

  function testCreate() {
    $args = array("one", "two");

    $mock = $this->getMock("stdClass");

    $adapter = $this->getMock("PhactoryGurl_Adapter");
    $adapter->expects($this->once())->method("create")->with("testClass", $args)->will($this->returnValue($mock));

    PhactoryGurl_Factory::setAdapter($adapter);

    $factory = $this->getMock("PhactoryGurl_Factory", array("getArgs"));
    $factory->expects($this->once())->method("getArgs")->will($this->returnValue($args));

    $factory->create("testClass", $args);

  }

  function testBasicEndToEnd() {
    $this->registerBasicBuilder();



    $factory = new PhactoryGurl_Factory;
    $ob = $factory->create("TestOb");

    $this->assertInstanceOf("TestOb", $ob);
    $this->assertEquals(["one", "two", "three"], $ob->arg);
  }

  function testMerge() {
    $this->registerBasicBuilder();

    $factory = new PhactoryGurl_Factory;

    $ob = $factory->create("TestOb", ["four", "five"]);
    $this->assertEquals(["one", "two", "three", "four", "five"], $ob->arg);
  }


  function testAdapterSave() {
    $mock = $this->getMock("stdClass");
    
    PhactoryGurl_Definitions::register("test", function() {});

    $adapter = $this->getMock("PhactoryGurl_Adapter", array("save", "create"));
    $adapter->expects($this->once())->method("create")->will($this->returnValue($mock));
    $adapter->expects($this->once())->method("save")->with($mock)->will($this->returnValue($mock));

    PhactoryGurl_Factory::setAdapter($adapter);

    $factory = new PhactoryGurl_Factory;
    $factory->build("test");


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
