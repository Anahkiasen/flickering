<?php
use Flickering\Flickering;

class FlickeringTest extends FlickeringTests
{
  public function testCanBuildNewFlickeringInstance()
  {
    $this->assertEquals('foo', $this->getDummyFlickering()->getApiKey());
  }

  public function testCanReturnAMethodObject()
  {
    $method = $this->getDummyFlickering()->callMethod('foobar');

    $this->assertInstanceOf('Flickering\Method', $method);
  }
}