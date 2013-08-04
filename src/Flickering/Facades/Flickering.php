<?php
/**
 * Facades\Flickering
 *
 * Static facade for Flickering
 */
namespace Flickering\Facades;

use Flickering\FlickeringServiceProvider;
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
			static::$app = FlickeringServiceProvider::make();
		}

		return 'flickering';
	}
}
