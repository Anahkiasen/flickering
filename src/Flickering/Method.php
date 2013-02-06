<?php
namespace Flickering;

use Underscore\Parse;
use Underscore\Types\Arrays;
use Underscore\Types\String;

class Method
{
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
   * The current instance of the Flickering
   * @var Flickering
   */
  protected $flickering;

  /**
   * POST parameters for the Method
   * @var array
   */
  protected $post;

  /**
   * Build a new Method
   *
   * @param Flickering $flickering The Flickering API
   * @param string     $method     The method being called
   */
  public function __construct(Flickering $flickering, $method, $parameters)
  {
    $this->flickering = $flickering;
    $this->method     = $method;
    $this->post       = $parameters;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// PUBLIC INTERFACE ////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Get the method being called
   *
   * @return string
   */
  public function getMethod()
  {
    return 'flickr.'.$this->method;
  }

  /**
   * Get the Method's endpoint
   *
   * @return string
   */
  public function getEndpoint()
  {
    // Prepare and format parameters
    $parameters = $this->prepareParameters($this->post);
    $parameters = $this->inlineParameters($parameters);

    return 'http://api.flickr.com/services/rest/?'.$parameters;
  }

  /**
   * Get the raw response from the Method
   *
   * @return array
   */
  public function getResponse()
  {
    return $this->send();
  }

  /**
   * Get the results of a method
   *
   * @return array
   */
  public function getResults()
  {
    $results = Arrays::first($this->send());

    return $results;
  }

  ////////////////////////////////////////////////////////////////////
  ///////////////////////// INTERNAL METHODS /////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Format the Method parameters
   *
   * @param array $parameters The parameters
   *
   * @return array
   */
  protected function prepareParameters($parameters)
  {
    return Arrays::from($parameters)
      ->merge(array(
        'method'         => $this->getMethod(),
        'api_key'        => $this->flickering->getApiKey(),
        'user'           => $this->flickering->getUser(),
        'format'         => $this->format,
        'nojsoncallback' => 1,
      ))
      ->sortKeys()
      ->clean()
      ->obtain();
  }

  /**
   * Inline parameters for a GET request
   *
   * @param array $parameters The parameters
   *
   * @return string
   */
  protected function inlineParameters($parameters)
  {
    return Arrays::from($parameters)
      ->each(function($value, $key) {
        return $key. '=' .$value;
      })
      ->values()
      ->implode('&')
      ->obtain();
  }

  /**
   * Send the request and get its results
   *
   * @return array
   */
  protected function send()
  {
    $request = curl_init($this->getEndpoint());
    curl_setopt($request, CURLOPT_POST, true);
    curl_setopt($request, CURLOPT_ENCODING, 'UTF-8');
    curl_setopt($request, CURLOPT_POSTFIELDS, $this->post);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_HTTPHEADER, array('Expect:'));

    $content = curl_exec($request);

    return Parse::fromJSON($content);
  }
}