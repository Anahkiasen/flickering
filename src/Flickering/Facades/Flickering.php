<?php
/**
 * Facades\Flickering
 *
 * Static facade for Flickering
 */
namespace Flickering\Facades;

use \Flickering\Flickering as FlickeringInstance;
use Illuminate\Support\Facades\Facade;

class Flickering extends Facade
{
  /**
   * Setup the static facade
   *
   * @param string $key    The API key
   * @param string $secret The API secret key
   */
  public static function handshake($key = null, $secret = null)
  {
    static::$resolvedInstance['flickering'] = new FlickeringInstance($key, $secret);
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
