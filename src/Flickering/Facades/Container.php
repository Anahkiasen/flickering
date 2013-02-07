<?php
/**
 * Facades\Container
 *
 * Builds, holds and returns various dependencies
 */
namespace Flickering\Facades;

use Illuminate\Cache\FileStore;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Container\Container as DependencyContainer;
use Symfony\Component\HttpFoundation\Session\Session;

class Container
{
  /**
   * Build a new IoC container
   */
  public function __construct()
  {
    $container = new DependencyContainer;

    $container->bind('Filesystem', 'Illuminate\Filesystem\Filesystem');
    $container->bind('FileLoader', function($container) {
      return new FileLoader($container['Filesystem'], __DIR__.'/../../..');
    });

    $container->bind('config', function($container) {
      return new Repository($container['FileLoader'], 'config');
    });

    $container->bind('cache', function($container) {
      return new FileStore($container->make('Filesystem'), __DIR__.'/../../../cache');
    });

    $container->singleton('session', function($container) {
      $session = new Session();
      if (!$session->isStarted()) $session->start();

      return $session;
    });

    $this->container = $container;
  }

  /**
   * Get the Cache instance
   *
   * @return Cache
   */
  public function getCache()
  {
    return $this->getDependency('cache');
  }

  /**
   * Get the Config instance
   *
   * @return Config
   */
  public function getConfig()
  {
    return $this->getDependency('config');
  }

  /**
   * Get the Session instance
   *
   * @return Session
   */
  public function getSession()
  {
    return $this->getDependency('session');
  }

  ////////////////////////////////////////////////////////////////////
  /////////////////////////// CORE METHODS ///////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Build required dependencies
   *
   * @param string $dependency A dependency to fretch from the container
   *
   * @return Container
   */
  protected function getDependency($dependency = null)
  {
    // If we provided a dependency, make it on the go
    if ($dependency) {
      return $this->container->make($dependency);
    }

    return $this->container;
  }
}
