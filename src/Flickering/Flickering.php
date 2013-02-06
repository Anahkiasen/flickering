<?php
namespace Flickering;

use Illuminate\Container\Container;
use Illuminate\Cache\FileStore;

class Flickering
{
  /**
   * The API key
   * @var string
   */
  protected $key;

  /**
   * The API secret key
   * @var string
   */
  protected $secret;

  /**
   * The Illuminate Container
   * @var Container
   */
  protected static $container;

  /**
   * Setup an instance of the API
   *
   * @param string $key    The API key
   * @param string $secret The API secret key
   */
  public function __construct($key, $secret)
  {
    $this->key    = $key;
    $this->secret = $secret;
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
    return new Method($this, $method, $parameters);
  }

  /**
   * Directly get the results of a method
   *
   * @param string $method     The method name
   * @param array  $parameters Its parameters
   *
   * @return Results
   */
  public function getResultsOf($method, $parameters = array())
  {
    return $this->callMethod($method, $parameters)->getResults();
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////////// INTERFACE ////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the user's API key
   *
   * @return string
   */
  public function getApiKey()
  {
    return $this->key;
  }

  /**
   * Get authentified user
   *
   * @return string
   */
  public function getUser()
  {
    return null;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// DEPENDENCIES ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Build required dependencies
   */
  protected function getDependency($dependency = null)
  {
    // If no Container available, build one
    if (!static::$container) {
      $container = new Container;
      $container->bind('Filesystem', 'Illuminate\Filesystem\Filesystem');
      $container->bind('cache', function($container) {
        return new FileStore($container->make('Filesystem'), __DIR__.'/../../cache');
      });

      static::$container = $container;
    }

    // If we provided a dependency, make it on the go
    if ($dependency) {
      return static::$container->make($dependency);
    }

    return static::$container;
  }

  /**
   * Get the Cache instance
   *
   * @return Cache
   */
  public function getCache()
  {
    return $this->getDependency('cache');
  }
  }
}
