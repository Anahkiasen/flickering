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

  public function testCanCorrectlyPrepareParameters()
  {
    $method = $this->getDummyMethod();
    $matcher = array(
      'api_key'        => "foo",
      'foo'            => "bar",
      'format'         => "json",
      'method'         => "flickr.foobar",
      'nojsoncallback' => 1
    );

    $this->assertEquals($matcher, $method->getParameters());
  }

  public function testCanExecuteRequests()
  {
    $container = Mockery::mock('Flickering\Facades\Container');
    $container->shouldReceive('getSession')->andReturn($this->getSession());
    $container->shouldReceive('getCache')->andReturn($this->getCache());
    $container->shouldReceive('getConfig')->andReturn($this->getConfig());

    $flickering = $this->getDummyFlickering();
    $flickering->setContainer($container);
    $method = $flickering->callMethod('photos.getPhotos', array('foo' => 'bar'));

    $method = $method->getResponse();
    $method = $method['message'];

    $this->assertEquals('Method "flickr.photos.getPhotos" not found', $method);
  }
}