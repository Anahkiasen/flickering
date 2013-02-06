<?php
/**
 * Helpers
 *
 * Various helpers
 */
namespace Flickering;

class Helpers
{
  /**
   * Send a request with cURL
   *
   * @param string $url        The URL
   * @param array $parameters  Its parameters
   *
   * @return mixed
   */
  public static function curl($url, $parameters = array())
  {
    // Setup base request headers
    $request = curl_init($url);
    curl_setopt($request, CURLOPT_ENCODING, 'UTF-8');
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_HTTPHEADER, array('Expect:'));

    // Append parameters if provided
    if ($parameters) {
      curl_setopt($request, CURLOPT_POST, true);
      curl_setopt($request, CURLOPT_POSTFIELDS, $parameters);
    }

    // Get results and parse them
    return curl_exec($request);
  }

  /**
   * Parses a query string
   *
   * @param string $query The query
   *
   * @return array
   */
  public static function parseQueryString($query)
  {
    foreach(explode('&', $query) as $segment) {
      list($key, $value) = explode('=', $segment);
      $parameters[$key] = $value;
    }

    return $parameters;
  }
}