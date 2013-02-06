<?php
/**
 * Facades\Flickering
 *
 * Static facade for Flickering
 */
namespace Flickering\Facades;

use Illuminate\Support\Facades\Facade;

class Flickering extends Facade
{
  /**
   * Setup the static facade
   *
   * @param string $key    The API key
   * @param string $secret The API secret key
   */
  public static function handshake($key, $secret)
  {
    static::$resolvedInstance['flickering'] = new \Flickering\Flickering($key, $secret);
  }

  /**
   * Redirect static calls to the instance
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
    return 'flickering';
  }
}