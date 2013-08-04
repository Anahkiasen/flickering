<?php
use Flickering\Facades\Flickering;

class FlickeringFacadeTest extends FlickeringTests
{
	public function testCanCreateInstanceFromFacade()
	{
		Flickering::handshake('foo', 'bar');
		$method = Flickering::photosetsGetPhotos();

		$this->assertInstanceOf('Flickering\Method', $method);
	}
}
