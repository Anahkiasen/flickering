<?php
use Flickering\Method;

class MethodTest extends FlickeringTests
{
  public function testCanPrefixMethods()
  {
    $method = $this->getDummyMethod();

    $this->assertEquals('flickr.foobar', $method->getMethod());
  }

  public function testCanChangeFormatOfTheCall()
  {
    $method = $this->getDummyMethod();
    $method->setFormat('xml');

    $this->assertEquals('xml', $method->getFormat());
  }
}