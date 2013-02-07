<?php
/**
 * Token
 *
 * A security token
 */
namespace Flickering\OAuth;

abstract class Token
{
  /**
   * His token
   * @var string
   */
  public $key;

  /**
   * His secret
   * @var string
   */
  public $secret;

  /**
   * Create new User
   *
   * @param string $key
   * @param string $secret
   */
  public function __construct($key, $secret)
  {
    $this->key    = $key;
    $this->secret = $secret;
  }
}