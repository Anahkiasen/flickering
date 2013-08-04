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

		$this->assertEquals($matcher, $method->getPreparedParameters());
	}

	public function testCanExecuteRequests()
	{
		$method = $this->flickering->callMethod('photos.getPhotos', array('foo' => 'bar'));

		$method = $method->getResponse();
		$method = $method['message'];

		$this->assertEquals('Method "flickr.photos.getPhotos" not found', $method);
	}

	public function testCanSetParameters()
	{
		$method = $this->getDummyMethod();
		$method->setParameter('api_key', 'changed');

		$parameters = $method->getParameters();
		$this->assertEquals('changed', $parameters['api_key']);
	}

	public function testCanElegantlySetParameters()
	{
		$method = $this->getDummyMethod();
		$method->setApiKey('changed');

		$parameters = $method->getParameters();
		$this->assertEquals('changed', $parameters['api_key']);
	}

	public function testCantCallWhateverYouWantOnMethodsGodDammit()
	{
		$this->setExpectedException('BadMethodCallException');

		$method = $this->getDummyMethod();
		$method->iLikeBigButts('and I cannot lie');
	}

	public function testCanElegantlyGetParameters()
	{
		$method = $this->getDummyMethod();
		$method->setApiKey('changed');

		$this->assertEquals('changed', $method->getApiKey());
	}
}
