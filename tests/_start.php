<?php
require __DIR__.'/../vendor/autoload.php';

use Flickering\Flickering;
use Flickering\Method;
use Flickering\OAuth\Consumer;
use Flickering\OAuth\User;
use Flickering\Request;

/**
 * Set up the Flickering tests
 */
abstract class FlickeringTests extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    $this->flickering = new Flickering('foo', 'bar');

    $dummyUser = $this->getDummyUser();
    $container = $this->flickering->getContainer();
    $container->bind('session', function() use ($dummyUser) {
      $session = Mockery::mock('Session');
      $session->shouldReceive('get')->with('flickering_oauth_user')->andReturn($dummyUser);
      $session->shouldReceive('has');

      return $session;
    });

    $this->flickering->setContainer($container);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// DUMMIES /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  protected function getDummyMethod()
  {
    return new Method($this->flickering, 'foobar', array('foo' => 'bar'));
  }

  protected function getDummyUser()
  {
    return new User(array(
      'credentials' => array(
        'token' => 'foo',
        'secret' => 'bar')));
  }

  protected function getDummyRequest($parameters = array(), $config = null)
  {
    $user     = $this->getDummyUser();
    $consumer = new Consumer('foo', 'bar');
    $cache    = $this->flickering->getContainer('cache');
    if (!$config) $config = $this->flickering->getContainer('config');

    return new Request($parameters, $consumer, $user, $cache, $config);
  }
}