<?php
class RequestTest extends FlickeringTests
{
	public function testCanCreateHashFromParameters()
	{
		$request = $this->getDummyRequest(array(
			'foo' => 'bar',
			'bis' => 'ter',
		));

		$this->assertEquals('bis-ter-foo-bar', $request->getHash());
	}

	public function testCacheTimeIsZeroIfCacheDisabled()
	{
		$config = Mockery::mock('Illuminate\Config\Repository');
		$config->shouldReceive('get')->with('config.cache.cache_requests')->andReturn(false);

		$request = $this->getDummyRequest(array(), $config);

		$this->assertEquals(0, $request->getCacheLifetime());
	}

	public function testCanGetCacheLifetime()
	{
		$config = Mockery::mock('Illuminate\Config\Repository');
		$config->shouldReceive('get')->with('config.cache.cache_requests')->andReturn(true);
		$config->shouldReceive('get')->with('config.cache.lifetime')->andReturn(20);

		$request = $this->getDummyRequest(array(), $config);

		$this->assertEquals(20, $request->getCacheLifetime());
	}

	public function testCanMakeRequests()
	{
		$request = $this->getDummyRequest(array(
			'method' => 'flickr.photos.getPhotos',
		));
		$request = $request->getResponse();
		$request = $request['err']['@attributes']['msg'];

		$this->assertEquals('Method "flickr.photos.getPhotos" not found', $request);
	}

	public function testCanGetRawResponse()
	{
		$request = $this->getDummyRequest(array(
			'method' => 'flickr.photos.getPhotos',
		));
		$request = $request->getRawResponse();

		$this->assertContains('<?xml', $request);
	}
}
