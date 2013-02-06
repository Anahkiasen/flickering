<?php
include '_start.php';

use Flickering\Flickering;

class FlickeringTest extends FlickeringTests
{
  public function testCanBuildNewFlickeringInstance()
  {
    $this->assertEquals('foo', $this->getFlickering()->getApiKey());
  }

  public function testCanReturnAMethodObject()
  {
    $method = $this->getFlickering()->callMethod('foobar');

    $this->assertInstanceOf('Flickering\Method', $method);
  }
}