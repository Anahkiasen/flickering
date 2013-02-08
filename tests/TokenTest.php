<?php
use Flickering\OAuth\User;

class TokenTest extends FlickeringTests
{
  public function testCanSetAndGetKeys()
  {
    $token = new User('foo', 'bar');

    $this->assertEquals('foo', $token->key);
    $this->assertEquals('bar', $token->secret);

  }
}