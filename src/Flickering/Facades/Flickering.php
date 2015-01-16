<?php
/**
 * Facades\Flickering
 * Static facade for Flickering
 */

namespace Flickering\Facades;

use Flickering\FlickeringServiceProvider;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

class Flickering extends Facade
{
    /**
     * Redirect static calls to the instance
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        if (!static::$app) {
            $app      = new Container();
            $provider = new FlickeringServiceProvider($app);
            $provider->register();

            static::$app = $app;
        }

        return 'flickering';
    }
}
