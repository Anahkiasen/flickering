<?php
/**
 * Request
 *
 * Sends requests against the API
 */
namespace Flickering;

use Illuminate\Cache\FileStore as Cache;
use Illuminate\Config\Repository as Config;
use Themattharris\TmhOAuth;
use Underscore\Parse;
use Underscore\Types\Arrays;

class Request
{
  /**
   * Make a new Request
   *
   * @param string $url  The URL
   * @param array $post  POST parameters
   */
  public function __construct(Flickering $flickering, Method $method)
  {
    $this->apiKey     = $flickering->getApiKey();
    $this->apiSecret  = $flickering->getApiSecret();
    $this->url        = $flickering->getEndpoint();
    $this->userToken  = $flickering->getUserToken();
    $this->userSecret = $flickering->getUserSecret();
    $this->parameters = $method->getParameters();
    $this->flickering = $flickering;
    $this->hash = $this->getHash($method);
  }

  /**
   * Get the caching hash of the Request
   *
   * @param Method $method The Method to hash
   *
   * @return string
   */
  protected function getHash(Method $method)
  {
    $parameters = $this->parameters;
    $parameters['method'] = $method->getMethod();
    $parameters = Arrays::sortKeys($parameters);
    $parameters = Arrays::clean($parameters);

    $hash = array();
    foreach ($parameters as $k => $v) $hash[] = $k.'-'.$v;

    return implode('-', $hash);
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
   * @return Results
   */
  public function getResults()
  {
    $results = $this->execute();
    $results = Results::from($results)->first();

    return $results;
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Check if a cache of the request exists
   *
   * @return boolean
   */
  protected function getCacheLifetime()
  {
    // If cache disabled, always return false
    if (!$this->flickering->getConfig()->get('config.cache.cache_requests')) {
      return 0;
    }

    return $this->flickering->getConfig()->get('config.cache.lifetime');
  }

  /**
   * Check if we can make the request and if it's cached
   *
   * @return array
   */
  protected function execute()
  {
    $me = $this;

    return $this->flickering->getCache()->remember($this->hash, $this->getCacheLifetime(), function() use ($me) {
      $request = new TmhOAuth(array(
        'consumer_key'    => $me->apiKey,
        'consumer_secret' => $me->apiSecret,
        'host'            => $me->url,
        'use_ssl'         => false,
        'user_token'      => $me->userToken,
        'user_secret'     => $me->userSecret,
      ));
      $request->request('GET', $request->url(''), $me->parameters);
      $content = Parse::fromJSON($request->response['response']);

      return $content;
    });
  }
}
