<?php
/**
 * Request
 *
 * Sends requests against the API
 */
namespace Flickering;

use Illuminate\Cache\FileStore as Cache;
use Illuminate\Config\Repository as Config;
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
  public function __construct(Cache $cache, Config $config, Method $method)
  {
    $this->cache      = $cache;
    $this->config     = $config;
    $this->url        = $method->getEndpoint();
    $this->parameters = $method->getParameters();
    $this->hash       = $this->getHash($method);
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
  protected function cacheExists()
  {
    // If cache disabled, always return false
    if (!$this->config->get('config.cache.cache_requests')) {
      return false;
    }

    return $this->cache->has($this->hash);
  }

  /**
   * Check if we can make the request and if it's cached
   *
   * @return array
   */
  protected function execute()
  {
    if ($this->cacheExists()) return $this->cache->get($this->hash);

    // Prepare request
    $url        = $this->url;
    $parameters = $this->parameters;
    $lifetime   = $this->config->get('config.cache.lifetime');

    return $this->cache->remember($this->hash, $lifetime, function() use ($url, $parameters) {
      $request = curl_init($url);
      curl_setopt($request, CURLOPT_POST, true);
      curl_setopt($request, CURLOPT_ENCODING, 'UTF-8');
      curl_setopt($request, CURLOPT_POSTFIELDS, $parameters);
      curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($request, CURLOPT_HTTPHEADER, array('Expect:'));

      // Get results and parse them
      $content = curl_exec($request);
      $content = Parse::fromJSON($content);

      return $content;
    });
  }
}
