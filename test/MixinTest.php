<?php

class MixinTest extends PHPUnit_Framework_TestCase {
  use PhactoryGurl;

  function testBuildCalled() {

    $factory = $this->getMock("PhactoryGurl_Factory");

    $factory->expects($this->once())->method("build");

    PhactoryGurl::setFactory($factory);

    $this->build("whatever");
  }
}
