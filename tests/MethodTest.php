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

  public function testCanCorrectlyComputeEndpoint()
  {
    $method = $this->getDummyMethod();
    $method->setFormat('xml');
    $endpoint =
      'http://api.flickr.com/services/rest/?'.
      'api_key=foo&'.
      'foo=bar&'.
      'format=xml&'.
      'method=flickr.foobar&'.
      'nojsoncallback=1';

    $this->assertEquals($endpoint, $method->getEndpoint());
  }
}