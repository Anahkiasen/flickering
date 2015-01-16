<?php
namespace Flickering;

use Flickering\Facades\Flickering;
use Flickering\TestCases\FlickeringTestCase;

class FlickeringFacadeTest extends FlickeringTestCase
{
	public function testCanCreateInstanceFromFacade()
	{
		Flickering::handshake('foo', 'bar');
		$method = Flickering::photosetsGetPhotos();

		$this->assertInstanceOf('Flickering\Method', $method);
	}
}
