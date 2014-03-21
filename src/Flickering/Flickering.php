<?php
namespace Flickering;

use BadMethodCallException;
use Illuminate\Container\Container;
use Opauth;

/**
 * Main interface for the Flickering package
 * Creates calls to methods and handles configuration
 */
class Flickering
{
	/**
	 * The IoC Container
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * The API
	 *
	 * @var Consumer
	 */
	protected $consumer;

	/**
	 * The User
	 *
	 * @var User
	 */
	protected $user;

	/**
	 * The Flickr API endpoint
	 *
	 * @var string
	 */
	const API_URL = 'api.flickr.com/services/rest/?';

	/**
	 * Setup an instance of the API
	 *
	 * @param Container $app
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	/**
	 * Set the API credentials
	 *
	 * @param string    $key       The API key
	 * @param string    $secret    The API secret key
	 */
	public function handshake($key = null, $secret = null)
	{
		// Create Consumer
		if (!$key)    $key    = $this->getOption('api_key');
		if (!$secret) $secret = $this->getOption('api_secret');

		$this->consumer = new OAuth\Consumer($key, $secret);
	}

	/**
	 * Aliased calls
	 *
	 * @return Method
	 */
	public function __call($method, $parameters)
	{
		// Catch aliased calls
		if ($alias = $this->callMethodByAlias($method, $parameters)) {
			return $alias;
		}

		throw new BadMethodCallException('Method ' .$method. ' does not exist');
	}

	/**
	 * Call a method on the current API
	 *
	 * @param string $method     The method name
	 * @param array  $parameters Its parameters
	 *
	 * @return Method
	 */
	public function callMethod($method, $parameters = array())
	{
		return new Method($this->app, $method, $parameters);
	}

	/**
	 * Call a method by its alias
	 *
	 * @param string $method     A method alias
	 * @param array  $parameters The arguments array
	 *
	 * @return Method
	 */
	protected function callMethodByAlias($method, $parameters)
	{
		$aliases = $this->getOption('methods');

		if (!$aliases or !array_key_exists($method, $aliases)) return false;

		// Get actual method name and arguments
		$argumentList = $aliases[$method];
		$method = preg_replace_callback('/[A-Z]/', function($match) {
			return '.'.strtolower($match[0]);
		}, $method, 1);

		// Rebuild parameters array
		foreach ($argumentList as $key => $argument) {
			$arguments[$argument] = array_get($parameters, $key);
		}

		return $this->callMethod($method, $arguments);
	}

	/**
	 * Directly get raw response of Method
	 *
	 * @param string $method     The method name
	 * @param array  $parameters Its parameters
	 *
	 * @return string
	 */
	public function getRawResponseOf($method, $parameters = array())
	{
		return $this->callMethod($method, $parameters)->getRawResponse();
	}

	/**
	 * Directly get response of Method
	 *
	 * @param string $method     The method name
	 * @param array  $parameters Its parameters
	 *
	 * @return array
	 */
	public function getResponseOf($method, $parameters = array())
	{
		return $this->callMethod($method, $parameters)->getResponse();
	}

	/**
	 * Directly get the results of a method
	 *
	 * @param string $method     The method name
	 * @param array  $parameters Its parameters
	 * @param string $subresults Subresults to fetch
	 *
	 * @return Results
	 */
	public function getResultsOf($method, $parameters = array(), $subresults = null)
	{
		return $this->callMethod($method, $parameters)->getResults($subresults);
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// INTERFACE ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the API Consumer
	 *
	 * @return Consumer
	 */
	public function getConsumer()
	{
		return $this->consumer;
	}

	/**
	 * Get the currently authentified User
	 *
	 * @return User
	 */
	public function getUser()
	{
		if ($this->isAuthentified() and $this->user) {
			return $this->user;
		}

		$user = $this->app['session']->get('flickering_oauth_user');
		if (!$user) {
			$user = new OAuth\User();
		}

		return $this->user = $user;
	}

	/**
	 * Check if we have an authentified User
	 *
	 * @return boolean
	 */
	public function isAuthentified()
	{
		return $this->app['session']->has('flickering_oauth_user');
	}

	/**
	 * Get an option from the config file
	 *
	 * @param string $option   The option to fetch
	 * @param mixed  $fallback A fallback
	 *
	 * @return mixed
	 */
	public function getOption($option, $fallback = null)
	{
		return $this->app['config']->get('flickering::'.$option, $fallback);
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// OPAUTH //////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the Opauth configuration to use for Flickering
	 *
	 * @return array
	 */
	protected function getOpauthConfiguration()
	{
		$config = $this->app['config']->get('flickering::opauth');

		// Set additional configuration options
		$config['strategy_dir']                 = __DIR__.'/../vendor/flickr';
		$config['Strategy']['Flickr']['key']    = $this->consumer->getKey();
		$config['Strategy']['Flickr']['secret'] = $this->consumer->getSecret();
		$config['callback_transport']           = 'post';

		return $config;
	}

	/**
	 * Return Opauth instance for authentification
	 *
	 * @return Opauth
	 */
	public function getOpauth()
	{
		return new Opauth($this->getOpauthConfiguration());
	}

	/**
	 * Process the post-authentification response
	 */
	public function getOpauthCallback()
	{
		new Opauth($this->getOpauthConfiguration(), false);

		// Store User credentials into session
		if (isset($_POST['opauth'])) {
			$response = unserialize(base64_decode($_POST['opauth']));
			$user = new OAuth\User($response['auth']);

			$this->app['session']->set('flickering_oauth_user', $user);
		}
	}
}
