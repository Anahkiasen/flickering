<?php
namespace Flickering;

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
   * Setup an instance of the API
   *
   * @param string $key    The API key
   * @param string $secret The API secret hash
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
   * @param array  $parameters Parameters for the method
   *
   * @return Method
   */
  public function callMethod($method, $parameters = array())
  {
    return new Method($this, $method, $parameters);
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
}