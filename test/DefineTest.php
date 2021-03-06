<?php

class PhactoryGurl_Test_Definition extends PHPUnit_Framework_TestCase {
  function testDefinitions() {
    $func = function() {};
    PhactoryGurl_Definitions::register("test_class", $func);

    $this->assertTrue(PhactoryGurl_Definitions::registered("test_class"));
    $this->assertEquals(
      ["class" => "test_class", "builder" => $func], PhactoryGurl_Definitions::definition("test_class")
    );
    $this->assertEquals($func, PhactoryGurl_Definitions::builder("test_class"));

  }


  function testDifferentClass() {
    PhactoryGurl_Definitions::register("test_class", array("class" => "TestClass"), function() {});
    $definition = PhactoryGurl_Definitions::definition("test_class");
    $this->assertEquals("TestClass", $definition["class"]);
  }
  
}
