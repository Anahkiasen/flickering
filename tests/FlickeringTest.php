<?php
use Flickering\Flickering;

class FlickeringTest extends FlickeringTests
{
	public function testCanBuildNewFlickeringInstance()
	{
		$this->assertEquals('foo', $this->flickering->getConsumer()->getKey());
	}

	public function testCanReturnAMethodObject()
	{
		$method = $this->flickering->callMethod('foobar');

		$this->assertInstanceOf('Flickering\Method', $method);
	}

	public function testCanGetOptionFromConfig()
	{
		$config = $this->flickering->getOption('api_key');

		$this->assertEquals('', $config);
	}

	public function testCanUseMethodShortcuts()
	{
		$method = $this->flickering->photosetsGetList('photoset', 10, 20);
		$matcher = array(
			'user_id'        => 'photoset',
			'page'           => 10,
			'per_page'       => 20,
			'api_key'        => 'foo',
			'format'         => 'json',
			'method'         => 'flickr.photosets.getList',
			'nojsoncallback' => 1
		);

		$this->assertInstanceOf('Flickering\Method', $method);
		$this->assertEquals('flickr.photosets.getList', $method->getMethod());
		$this->assertEquals($matcher, $method->getPreparedParameters());
	}
}
