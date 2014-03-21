# Flickering [![Build Status](https://next.travis-ci.org/Anahkiasen/flickering.png?branch=master)](https://next.travis-ci.org/Anahkiasen/flickering)

Flickering is a next-generation PHP API for the [Flickr][] photos sharing social network.
It's a **work in progress** but it already works so don't worry about that.

You can get it on Composer, in order to do so just add it to your application like this :

```json
"anahkiasen/flickering": "*"
```

After updating composer, add the ServiceProvider to the providers array in app/config/app.php

```php
'Flickering\FlickeringServiceProvider',
```

If you want to use the facade, also add that to app.php:

```php
 'Flickering' => 'Flickering\Facades\Flickering',
 ```

If you want to use store the api keys/secret in the config, publish the config files.

    $ php artisan config:publish anahkiasen/flickering

## Using Flickering

### Creating a new instance

To use Flickering you will of course need an API key from Flickr, if you don't already have on you get can [one here][], they're nice and all so they'll give you one for free.
Once you have that, set it in Flickering's `config/config.php` file. Or if you don't want/can, you can always pass your credentials to the constructor.

To start working with Flickering, create a new instance of it like the example underneath. If you set your API credentials in the config file, you don't need to pass any arguments to the constructor as it will automatically fetch your key and secret key from the config files.

```php
$flickering = App::make('flickering');
$flickering->handshake($apiKey, $apiSecret);
```

If working with instances is not your thing, Flickering also uses [Illuminate][]'s Facade component to provide a static interface to all its methods. You can create a new static instance of Flickering like this. Arguments are facultative if config file is set, same as above.

```php
Flickering::handshake($apiKey, $apiSecret)
Flickering::handshake();    //Using the config file
```

Before doing anything with the Flickr api, you have to call the handshake (once).

### Calling methods on the Flickr API

Flickering provides several ways to make calls to the API. They differ in their level of syntaxic elegance and the level of control you get on the API response.

The most basic (but also the most powerful) way is to simply call the `->callMethod` on your Flickering instance :

```php
Flickering::callMethod('people.getPhotos', array('user_id' => '31667913@N06'));
```

Alternatively, Flickering is set up with a bunch of smart aliases for common methods, so to the exact same thing as the example above you can also do the following.

```php
Flickering::peopleGetPhotos('31667913@N06')
```

Note that the arguments for each smart alias are mapped from the list of arguments provided by the _Flickr_ API, so in the example above we're actually calling the `flickr.people.getPhotos` method, meaning the number and order of its arguments can be found in the [API docs][].

Since changing that order of arguments would be messy, smart alias are mostly to be used when you want quick calls to method with few arguments — if you have to set every goddamn one of the method's arguments, it's recommanded to use `callMethod` instead — for the simple reason that with an associative array at least you'll always plainly see which argument maps to what.

### Getting results from a call to the API

Now what we've just seen is all pretty basic stuff, and doesn't differ from most Flickr API implementations out there. Here is where stuff gets interesting. When you use one of the methods above to make a call on the API you don't directly get the results as a raw JSON string (although you can). By default, Flickering will return a **Method** object from which you can do various interesting stuff.

First, you can manipulate the results after the initial call, either via `->setParameter` or via the elegant aliases of Flickering.

```php
$method = Flickering::peopleGetPhotos('31667913@N06')

$method->setParameter('per_page', 25) // or
$method->setPerPage(25)

$method->getPerPage() // 25
```

From there, **Method** has three methods for you to get your results from : `->getRawResponse`, `->getReponse` and `->getResults`.

* The first one as its name suggests just returns the raw response from Flickr, unparsed and untouched.
* The second one returns the original response, but parses it from JSON/XML/etc to an actual PHP array
* The third one returns a **Results** instance, and takes a single parameter a facultative subset of the results you might want to fetch directly. Per example when you get photos from a method of the Flickr API, the actual photos will be nested in a `photos` key of the response array. You can get them directly by doing `->getResults('photos')`. You can get nested results via dot-notation too : `->getResults('photos.0')`.

The **Results** class leverages the power behind [Underscore.php][] to create a live repository of your results, allowing you to easily manipulate them and fetch deeply nested informations from them. You can get a glimpse of the manipulation power brought by Underscore in the [Arrays docs][] and I also recommand quickly checking out what [Underscore Repositories][] are and what they can do.

The whole _Flickering > Method > Request_ process is bypassable via the matching methods on the **Flickering** instance : `->getRawResponseOf`, `->getResponseOf` and `->getResultsOf`. So the two examples below do the exact same thing, just faster :

```php
$method = Flickering::callMethod('people.getPhotos', array('user_id' => '31667913@N06'))
$results = $method->getResults('photos')

// Same thing
$results = Flickering::getResultsOf('people.getPhotos', array('user_id' => '31667913@N06'))
```

### Authentified calls

As the Flickr API is now powered by [OAuth][], making authentified requests to it will require a permission from the user. In order to speed up the process, Flickering has out-of-the-box an [Opauth][] _Strategy_ set up.

If you're using Flickering with your favorite framework, use it's _Router_ to leverage Flickering's `getOpauth` and `getOpauthCallback` methods. The first one must be **returned** in the first two steps of the process, the second one must just be present somewhere in your callback page (can be the same page you'll do your calls from once the user has given permission).

Here is an example implementation with the [Laravel][] framework :

```php
Route::get('flickr/auth', function() {
    Flickering::handshake();
    return Flickering::getOpauth();
});
Route::any('flickr/oauth_callback', function() {
    Flickering::handshake();
    if(Request::getMethod() == 'POST'){
        Flickering::getOpauthCallback();
        return 'Authenticated!';
    }else{
        Flickering::getOpauth();
        return 'Being redirected..';
    }
});
```

If you're using any framework and just want to make some requests on a plain old PHP page, an example implementation via a basic router and _.htaccess_ is demonstrated in the `example` folder of the repository.

### Working with the User

Once the user has been logged in you can get its informations via the `Flickering::getUser()` method which will return an **User** object containing the various informations sent back by the OAuth process. Here are some of the methods available :

```php
Flickering::handshake();
$user = Flickering::getUser();

// Get OAuth token
$user->getKey()

// Get Flickr's UID of the person
$user->getUid()

// Get an array of basic informations on the person
$user->getInformations()

// Get the whole schebang : photos, photosets, friends, and other informations made public by the user
$user->getPerson()
```

Moreover, Flickering has a `isAuthentified` method for you to use that will check whether OAuth credentials are available or not.

## That's all folks !

Don't forget to post any issue/bug/request in the Github Issues.

[API docs]: http://www.flickr.com/services/api/explore/flickr.people.getPhotos
[Arrays docs]: https://github.com/Anahkiasen/underscore-php/wiki/Arrays
[Flickr]: http://www.flickr.com/
[Illuminate]: https://github.com/illuminate/support
[Laravel]: http://laravel.com/
[OAuth]: http://oauth.net/
[one here]: http://www.flickr.com/services/apps/create/apply/
[Opauth]: http://opauth.org/
[Underscore Repositories]: https://github.com/Anahkiasen/underscore-php/wiki/Repository
[Underscore.php]: http://anahkiasen.github.com/underscore-php
