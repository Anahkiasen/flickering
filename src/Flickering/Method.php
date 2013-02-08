<?php
/**
 * Method
 *
 * A method being called on the API
 */
namespace Flickering;

use Underscore\Types\Arrays;
use Underscore\Types\String;

class Method
{
  /**
   * The current instance of the Flickering
   * @var Flickering
   */
  protected $flickering;

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
   * @param Flickering $flickering The Flickering API
   * @param string     $method     The method being called
   */
  public function __construct(Flickering $flickering, $method, $parameters = array())
  {
    $this->flickering = $flickering;
    $this->method     = $method;
    $this->parameters = $parameters;
  }

  ////////////////////////////////////////////////////////////////////
  ////////////////////////// PUBLIC INTERFACE ////////////////////////
  ////////////////////////////////////////////////////////////////////

  // Request format ------------------------------------------------ /

  /**
   * Change the format of the method
   *
   * @param string $format The format
   */
  public function setFormat($format)
  {
    $this->format = $format;

    return $this;
  }

  /**
   * Get the format of the method
   *
   * @return string
   */
  public function getFormat()
  {
    return $this->format;
  }

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

  /**
   * Get the Method parameters
   *
   * @return array
   */
  public function getParameters()
  {
    return $this->prepareParameters($this->parameters);
  }

  // Method results ------------------------------------------------ /

  /**
   * Get the raw response from the Method
   *
   * @return array
   */
  public function getResponse()
  {
    return $this->createRequest()->getRawResponse();
  }

  /**
   * Get the actual results of a method
   *
   * @return Results
   */
  public function getResults()
  {
    return $this->createRequest()->getResults();
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
      $this->getParameters(),
      $this->flickering->getConsumer(),
      $this->flickering->getUser(),
      $this->flickering->getContainer()->getCache(),
      $this->flickering->getContainer()->getConfig()
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
    return Arrays::from($parameters)
      ->merge(array(
        'method'         => $this->getMethod(),
        'api_key'        => $this->flickering->getConsumer()->key,
        'format'         => $this->format,
        'nojsoncallback' => 1,
      ))
      ->sortKeys()
      ->clean()
      ->obtain();
  }
}
