<?php
include '_start.php';

use Flickering\Flickering;

class FlickeringTest extends FlickeringTests
{
  public function testCanBuildNewFlickeringInstance()
  {
    $flickering = new Flickering('foo', 'bar');
    $this->assertEquals('foo', $flickering->getApiKey());
  }
}