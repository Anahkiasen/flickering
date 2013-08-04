<?php
use Flickering\OAuth\User;

class TokenTest extends FlickeringTests
{
	public function testCanSetAndGetKeys()
	{
		$token = new User(array(
			'credentials' => array(
				'token' => 'foo',
				'secret' => 'bar')
			)
		);

		$this->assertEquals('foo', $token->getKey());
		$this->assertEquals('bar', $token->getSecret());
	}
}
