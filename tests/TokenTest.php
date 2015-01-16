<?php
namespace Flickering;

use Flickering\OAuth\User;
use Flickering\TestCases\FlickeringTestCase;

class TokenTest extends FlickeringTestCase
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
