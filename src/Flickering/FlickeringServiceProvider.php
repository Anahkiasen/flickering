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
		// ...
	}

	/**
	 * Bind classes and commands
	 *
	 * @return void
	 */
	public function boot()
	{
		// Register classes and commands
		$this->app = static::make($this->app);
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
	 * Make a Rocketeer container
	 *
	 * @return Container
	 */
	public static function make($app = null)
	{
		if (!$app) {
			$app = new Container;
		}

		$serviceProvider = new static($app);

		// Bind classes
		$app = $serviceProvider->bindCoreClasses($app);
		$app = $serviceProvider->bindClasses($app);

		return $app;
	}

	/**
	 * Bind the core classes
	 *
	 * @param  Container $app
	 *
	 * @return Container
	 */
	public function bindCoreClasses(Container $app)
	{
		$app->bindIf('request', function ($app) {
			return Request::createFromGlobals();
		}, true);

		$app->bindIf('config', function ($app) {
			$fileloader = new FileLoader(new Filesystem, __DIR__.'/../config');

			return new Config($fileloader, 'config');
		}, true);

		$app->bindIf('cache', function($app) {
			$fileStore = new FileStore(new Filesystem, __DIR__.'/../../cache');

			return new Cache($fileStore);
		});

		$app->bindIf('session', function ($app) {
			return new Session;
		}, true);

		// Register config file
		$app['config']->package('anahkiasen/flickering', __DIR__.'/../config');

		return $app;
	}

	/**
	 * Bind the Rocketeer classes to the Container
	 *
	 * @param  Container $app
	 *
	 * @return Container
	 */
	public function bindClasses(Container $app)
	{
		$app->singleton('flickering', function ($app) {
			return new Flickering($app);
		});

		return $app;
	}
}
