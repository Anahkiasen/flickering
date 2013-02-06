Flickering
----------

[![Build Status](https://next.travis-ci.org/Anahkiasen/flickering.png?branch=master)](https://next.travis-ci.org/Anahkiasen/flickering)

Flickering is a next-generation PHP API for the _Flickr_ photos sharing social network. It's a **work in progress**.

It's available on Composer and you can add it to your application like this :

```json
"anahkiasen/flickering": "dev-master"
```

Using Flickering
================

You can create a new instance of Flickering like this :

```php
$flickering = new Flickering($apiKey, $apiSecret);
```

From there everything goes through that instance. You can simply call a method with the `->callMethod` method. Now, this is where Flickering differs from most Flickr clients available — what is returned is not directly the results but a **Method** object. This method sums up the query you're doing against the API. From there you can get various informations from it via the available methods.

To get the results, two choices : either call `->getResponse` on the **Method** object to get the raw response — although Flickering will still parse it to a PHP-friendly array. Or call `->getResults` which will return an instance of the **Results** class.

The **Results** class leverages the power behind [Underscore.php][] to create a live repository of your results, allowing you to easily manipulate them and fetch deeply nested informations from them.
You can immediatly get a Results object by doing `->getResultsOf($method, $methodParameters)` like this :

```php
$results = Flickering::getResultsOf('people.getInfo', array('user_id' => 'USERID'));
echo $results->get('username');
```

Static interface
================

Flickering also uses [Illuminate][]'s Facade component to provide a static interface to Flickering. You can create a new static instance of Flickering via the `Flickering::handshake($apiKey, $apiSecret)` method.

[Underscore.php]: http://anahkiasen.github.com/underscore-php
[Illuminate]: https://github.com/illuminate/support