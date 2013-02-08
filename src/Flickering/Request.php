<?php
/**
 * Request
 *
 * Sends requests against the API
 */
namespace Flickering;

use Flickering\OAuth\Consumer;
use Flickering\OAuth\User;
use Illuminate\Cache\FileStore as Cache;
use Illuminate\Config\Repository as Config;
use Themattharris\TmhOAuth;
use Underscore\Parse;
use Underscore\Types\Arrays;
use Underscore\Types\String;

class Request
{
  /**
   * The OAuth Consumer
   * @var Consumer
   */
  public $consumer;

  /**
   * The OAuth User
   * @var User
   */
  public $user;

  /**
   * Instance of the Cache class
   * @var Cache
   */
  protected $cache;

  /**
   * Instance of the Config class
   * @var Config
   */
  protected $config;

  /**
   * The POST parameters to request
   * @var array
   */
  protected $parameters;

  /**
   * What to cache the request as
   * @var string
   */
  protected $hash;

  /**
   * Create a new Request
   *
   * @param array      $parameters The request POST parameters
   * @param Consumer   $consumer   The Consumer
   * @param User       $user       The User
   * @param Flickering $flickering The Flickering instance
   */
  public function __construct($parameters, Consumer $consumer, User $user, Cache $cache, Config $config)
  {
    // OAuth
    $this->consumer = $consumer;
    $this->user     = $user;

    // Dependencies
    $this->cache  = $cache;
    $this->config = $config;

    // Request parameters
    $this->parameters = $parameters;
    $this->hash       = $this->createHashFromParameters($parameters);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// RESULTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the Request cache hash
   *
   * @return string
   */
  public function getHash()
  {
    return $this->hash;
  }

  /**
   * Get the parsed response from the Request
   *
   * @return array
   */
  public function getResponse()
  {
    return $this->execute();
  }

  /**
   * Get the raw response from the Request
   *
   * @return string
   */
  public function getRawResponse()
  {
    return $this->execute(false);
  }

  /**
   * Get the results of the Request as a Results instance
   *
   * @param string $subresults A subresult array to fetch from results
   *
   * @return Results
   */
  public function getResults($subresults = null)
  {
    $results = $this->execute();

    // Fetch results from sub-arrays
    $results = Results::from($results)->first();
    if ($subresults) $results = $results->get($subresults);

    return $results;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the lifetime of the cache
   *
   * @return integer
   */
  public function getCacheLifetime()
  {
    // If cache disabled, always return false
    if (!$this->config->get('config.cache.cache_requests')) {
      return 0;
    }

    return (int) $this->config->get('config.cache.lifetime');
  }

  /**
   * Get the caching hash of the Method
   *
   * @param array $parameters The parameters to hash
   *
   * @return string
   */
  protected function createHashFromParameters($parameters)
  {
    $parameters = Arrays::sortKeys($parameters);
    $parameters = Arrays::clean($parameters);

    $hash = array();
    foreach ($parameters as $k => $v) $hash[] = $k.'-'.$v;

    return implode('-', $hash);
  }

  /**
   * Execute the Request against the API
   *
   * @return array
   */
  protected function execute($parse = true)
  {
    $_this = $this;

    return $this->cache->remember($this->hash, $this->getCacheLifetime(), function() use ($parse, $_this) {

      // Create OAuth request
      $request = new TmhOAuth(array(
        'consumer_key'    => $_this->consumer->getKey(),
        'consumer_secret' => $_this->consumer->getSecret(),
        'host'            => Flickering::API_URL,
        'use_ssl'         => false,
        'user_token'      => $_this->user->getKey(),
        'user_secret'     => $_this->user->getSecret(),
      ));
      $request->request('GET', $request->url(''), $_this->parameters);
      $response = $request->response['response'];

      // Return raw if requested
      if (!$parse) return $response;

      // Parse resulting content
      switch (Arrays::get($_this->parameters, 'format')) {
        case 'json':
          return Parse::fromJSON($response);
        default:
          return Parse::fromXML($response);
      }
    });
  }
}
