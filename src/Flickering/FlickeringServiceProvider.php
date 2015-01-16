<?php
namespace Flickering;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository as Config;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Bind the various Flickering classes to Laravel
 */
class FlickeringServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->bindCoreClasses();
        $this->bindClasses();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('flickering');
    }

    ////////////////////////////////////////////////////////////////////
    /////////////////////////// CLASS BINDINGS /////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Bind the core classes
     */
    public function bindCoreClasses()
    {
        $this->app->bindIf('request', function () {
            return Request::createFromGlobals();
        }, true);

        $this->app->bindIf('config', function () {
            $fileloader = new FileLoader(new Filesystem(), __DIR__.'/../config');

            return new Config($fileloader, 'config');
        }, true);

        $this->app->bindIf('cache', function () {
            $fileStore = new FileStore(new Filesystem(), __DIR__.'/../../cache');

            return new Cache($fileStore);
        });

        $this->app->bindIf('session', function () {
            return new Session();
        }, true);

        // Register config file
        $this->app['config']->package('anahkiasen/flickering', __DIR__.'/../config');
    }

    /**
     * Bind the Rocketeer classes to the Container
     *
     * @return Container
     */
    public function bindClasses()
    {
        $this->app->singleton('flickering', function ($app) {
            return new Flickering($app);
        });
    }
}
