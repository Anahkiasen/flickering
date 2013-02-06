<?php
use Flickering\Method;

class MethodTest extends FlickeringTests
{
  public function testCanPrefixMethods()
  {
    $method = new Method($this->getFlickering(), 'foobar');

    $this->assertEquals('flickr.foobar', $method->getMethod());
  }
}