<?php
/**
 * Request
 *
 * Sends requests against the API
 */
namespace Flickering;

use Flickering\Flickering;
use Flickering\OAuth\Consumer;
use Flickering\OAuth\User;
use Illuminate\Cache\FileStore as Cache;
use Illuminate\Config\Repository as Config;
use Themattharris\TmhOAuth;
use Underscore\Parse;
use Underscore\Types\Arrays;

class Request
{
  protected $apiKey;
  protected $apiSecret;
  protected $userToken;
  protected $userSecret;
  protected $cache;
  protected $config;
  protected $parameters;
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
    $this->apiKey     = $consumer->key;
    $this->apiSecret  = $consumer->secret;
    $this->userToken  = $user->key;
    $this->userSecret = $user->secret;

    // Dependencies
    $this->cache      = $cache;
    $this->config     = $config;

    // Request parameters
    $this->parameters = $parameters;
    $this->hash       = $this->createHashFromParameters($parameters);
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////////// RESULTS /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the raw response from the Request
   *
   * @return array
   */
  public function getRawResponse()
  {
    return $this->execute();
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
   * Get the lifetime of the cache
   *
   * @return integer
   */
  protected function getCacheLifetime()
  {
    // If cache disabled, always return false
    if (!$this->config->get('config.cache.cache_requests')) {
      return 0;
    }

    return $this->config->get('config.cache.lifetime');
  }

  /**
   * Check if we can make the request and if it's cached
   *
   * @return array
   */
  protected function execute()
  {
    $_this = $this;

    return $this->cache->remember($this->hash, $this->getCacheLifetime(), function() use ($_this) {
      $request = new TmhOAuth(array(
        'consumer_key'    => $_this->apiKey,
        'consumer_secret' => $_this->apiSecret,
        'host'            => Flickering::API_URL,
        'use_ssl'         => false,
        'user_token'      => $_this->userToken,
        'user_secret'     => $_this->userSecret,
      ));
      $request->request('GET', $request->url(''), $_this->parameters);
      $content = Parse::fromJSON($request->response['response']);

      return $content;
    });
  }
}
