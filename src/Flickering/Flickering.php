<?php
namespace Flickering;

use Opauth;
use BadMethodCallException;
use Illuminate\Cache\FileStore;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\Session\Session;
use Underscore\Types\Arrays;

class Flickering
{
  /**
   * The API
   * @var Consumer
   */
  protected $consumer;

  /**
   * The User
   * @var User
   */
  protected $user;

  /**
   * The Flickr API endpoint
   * @var string
   */
  const API_URL = 'api.flickr.com/services/rest/?';

  /**
   * A list of method aliases and their arguments
   * @var array
   */
  protected $aliases = array(
    'photosetsGetList'   => array('user_id', 'page', 'per_page'),
    'photosetsGetPhotos' => array('photoset_id', 'extras', 'privacy_filter', 'per_page', 'page', 'media'),
    'peopleGetPhotos'    => array('user_id', 'safe_search', 'min_upload_date', 'max_upload_date', 'min_taken_date', 'max_taken_date', 'content_type', 'privacy_filter', 'extras', 'per_page', 'page'),
  );

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
  public function __construct($key = null, $secret = null)
  {
    $key    = $key    ?: $this->getOption('api_key');
    $secret = $secret ?: $this->getOption('api_secret');

    $this->consumer = new OAuth\Consumer($key, $secret);
  }

  /**
   * Aliased calls
   *
   * @return Method
   */
  public function __call($method, $parameters)
  {
    if (array_key_exists($method, $this->aliases)) {

      // Get actual method name and arguments
      $argumentList = $this->aliases[$method];
      $method = preg_replace_callback('/[A-Z]/', function($match) {
        return '.'.strtolower($match[0]);
      }, $method, 1);

      // Rebuild parameters array
      foreach ($argumentList as $key => $argument) {
        $arguments[$argument] = Arrays::get($parameters, $key);
      }

      return $this->callMethod($method, $arguments);
    }

    throw new BadMethodCallException('The requested method "' .$method. '" does not exist');
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
    if ($this->user) return $this->user;

    return $this->user = $this->getSession()->get('flickering_oauth_user');
  }

  /**
   * Get the Flickr API endpoint
   *
   * @return string
   */
  public function getEndpoint()
  {
    return Flickering::API_URL;
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
    return $this->getConfig()->get('config.'.$option, $fallback);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// OPAUTH //////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the Opauth configuration to use for Flickering
   *
   * @return array
   */
  private function getOpauthConfiguration()
  {
    $config = $this->getConfig()->get('opauth');
    $config['strategy_dir'] = __DIR__.'/../vendor/flickr';
    $config['Strategy']['Flickr']['key'] = $this->consumer->key;
    $config['Strategy']['Flickr']['secret'] = $this->consumer->secret;

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
      $user = new OAuth\User($response['auth']['credentials']['token'], $response['auth']['credentials']['secret']);
      $this->getSession()->set('flickering_oauth_user', $user);
    }
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// DEPENDENCIES ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the Cache instance
   *
   * @return Cache
   */
  public function getCache()
  {
    return $this->getDependency('cache');
  }

  /**
   * Get the Config instance
   *
   * @return Config
   */
  public function getConfig()
  {
    return $this->getDependency('config');
  }

  /**
   * Get the Session instance
   *
   * @return Session
   */
  public function getSession()
  {
    return $this->getDependency('session');
  }

  /**
   * Build a new IoC container
   *
   * @return Container
   */
  protected function buildContainer()
  {
    $container = new Container;

    $container->bind('Filesystem', 'Illuminate\Filesystem\Filesystem');
    $container->bind('FileLoader', function($container) {
      return new FileLoader($container['Filesystem'], __DIR__.'/../..');
    });

    $container->bind('config', function($container) {
      return new Repository($container['FileLoader'], 'config');
    });

    $container->bind('cache', function($container) {
      return new FileStore($container->make('Filesystem'), __DIR__.'/../../cache');
    });

    $container->singleton('session', function($container) {
      $session = new Session();
      if (!$session->isStarted()) $session->start();

      return $session;
    });

    return $container;
  }

  /**
   * Build required dependencies
   */
  protected function getDependency($dependency = null)
  {
    // If no Container available, build one
    if (!static::$container) {
      static::$container = $this->buildContainer();
    }

    // If we provided a dependency, make it on the go
    if ($dependency) {
      return static::$container->make($dependency);
    }

    return static::$container;
  }
}
