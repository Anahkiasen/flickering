<?php
namespace Flickering;

use BadMethodCallException;
use Illuminate\Container\Container;
use Illuminate\Support\Str;

/**
 * A method being called on the API
 */
class Method
{
	/**
	 * The IoC Container
	 *
	 * @var Container
	 */
	protected $app;

	/**
	 * The method being called
	 * @var string
	 */
	protected $method;

	/**
	 * The format to return the response in
	 * @var string
	 */
	protected $format = 'json';

	/**
	 * Parameters for the Method
	 * @var array
	 */
	protected $parameters;

	/**
	 * Build a new Method
	 *
	 * @param Container $app
	 * @param string    $method
	 * @param array     $parameters
	 */
	public function __construct(Container $app, $method, $parameters = array())
	{
		$this->app        = $app;
		$this->method     = $method;
		$this->parameters = $parameters;
	}

	/**
	 * Elegant aliases for setting/getting parameters
	 *
	 * @param string $method     The parameter
	 * @param array  $parameters Its value
	 */
	public function __call($method, $parameters)
	{
		$realParameter = $this->getRealParameter($method);

		if (Str::startsWith($method, 'set')) {
			$this->setParameter($realParameter, $parameters[0]);
		} elseif (Str::startsWith($method, 'get')) {
			return array_get($this->parameters, $realParameter);
		} else {
			throw new BadMethodCallException('The method "' .$method. '" does not exist on the Method object');
		}

		return $this;
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// PARAMETERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the Method parameters
	 *
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * Get the prepared Method parameters
	 *
	 * @return array
	 */
	public function getPreparedParameters()
	{
		return $this->prepareParameters($this->parameters);
	}

	/**
	 * Set a parameter on the Method
	 *
	 * @param string $key   The key of the parameter
	 * @param mixed  $value Its value
	 */
	public function setParameter($key, $value)
	{
		$this->parameters[$key] = $value;
	}

	/**
	 * Set a parameter with a method alias
	 */
	protected function getRealParameter($parameter)
	{
		$parameter = substr($parameter, 3);
		$parameter = lcfirst($parameter);

		return Str::snake($parameter);
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////// PUBLIC INTERFACE ////////////////////////
	////////////////////////////////////////////////////////////////////

	// Informations about the method --------------------------------- /

	/**
	 * Get the method being called
	 *
	 * @return string
	 */
	public function getMethod()
	{
		return 'flickr.'.$this->method;
	}

	// Method results ------------------------------------------------ /

	/**
	 * Get the parsed response from the Request
	 *
	 * @return array
	 */
	public function getResponse()
	{
		return $this->createRequest()->getResponse();
	}

	/**
	 * Get the raw response from the Request
	 *
	 * @return string
	 */
	public function getRawResponse()
	{
		return $this->createRequest()->getRawResponse();
	}

	/**
	 * Get the actual results of a Request
	 *
	 * @return Results
	 */
	public function getResults($subresults = null)
	{
		return $this->createRequest()->getResults($subresults);
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////// INTERNAL METHODS /////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get a new Request instance
	 *
	 * @return Request
	 */
	protected function createRequest()
	{
		return new Request(
			$this->getPreparedParameters(),
			$this->app['flickering']->getConsumer(),
			$this->app['flickering']->getUser(),
			$this->app
		);
	}

	/**
	 * Format the Method parameters
	 *
	 * @param array $parameters The parameters
	 *
	 * @return array
	 */
	protected function prepareParameters($parameters)
	{
		$parameters = array_merge($parameters, array(
			'method'         => $this->getMethod(),
			'api_key'        => $this->app['flickering']->getConsumer()->getKey(),
			'format'         => $this->format,
			'nojsoncallback' => 1,
		));

		// Sort parameters and filter
		ksort($parameters);
		$parameters = array_filter($parameters);

		return $parameters;
	}
}
