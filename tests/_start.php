<?php
require __DIR__.'/../vendor/autoload.php';

use Flickering\FlickeringServiceProvider;
use Flickering\Method;
use Flickering\OAuth\Consumer;
use Flickering\OAuth\User;
use Flickering\Request;

/**
 * Set up the Flickering tests
 */
abstract class FlickeringTests extends PHPUnit_Framework_TestCase
{
	/**
	 * The IoC Container
	 *
	 * @var Container
	 */
	protected $app;

	/**
	 * Set up the tests
	 */
	public function setUp()
	{
		$this->app = FlickeringServiceProvider::make();
		$this->app['flickering']->handshake('foo', 'bar');
		$this->app['session'] = $this->mockSession($this->getDummyUser());
	}

	/**
	 * Get an instance on the container
	 *
	 * @param  string $instance
	 *
	 * @return object
	 */
	public function __get($instance)
	{
		return $this->app->make($instance);
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// DUMMIES /////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Mock the Session component
	 *
	 * @return Mockery
	 */
	protected function mockSession($dummyUser)
	{
		$session = Mockery::mock('Session');
		$session->shouldReceive('get')->with('flickering_oauth_user')->andReturn($dummyUser);
		$session->shouldReceive('has');

		return $session;
	}

	/**
	 * Get a dummy method instance
	 *
	 * @return Method
	 */
	protected function getDummyMethod()
	{
		return new Method($this->app, 'foobar', array('foo' => 'bar'));
	}

	/**
	 * Get a dummy User instance
	 *
	 * @return User
	 */
	protected function getDummyUser()
	{
		return new User(array(
			'credentials' => array(
				'token' => 'foo',
				'secret' => 'bar')));
	}

	/**
	 * Get a dummy request instance
	 *
	 * @param  array  $parameters
	 * @param  array $config
	 *
	 * @return Request
	 */
	protected function getDummyRequest($parameters = array(), $config = null)
	{
		$user     = $this->getDummyUser();
		$consumer = new Consumer('foo', 'bar');
		if ($config) {
			$this->app['config'] = $config;
		}

		return new Request($parameters, $consumer, $user, $this->app);
	}
}
