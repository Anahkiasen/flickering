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

  public function testCanGetOptionFromConfig()
  {
    $config = $this->getDummyFlickering()->getOption('api_key');

    $this->assertEquals('foo', $config);
  }

  public function testCanUseMethodShortcuts()
  {
    $method = $this->getDummyFlickering();
    $method = $method->photosetsGetList('photoset');

    $this->assertInstanceOf('Flickering\Method', $method);
    $this->assertEquals('flickr.photosets.getList', $method->getMethod());
    $this->assertEquals(array('user_id' => 'photoset', 'page' => null, 'per_page' => null), $method->getParameters());
  }
}