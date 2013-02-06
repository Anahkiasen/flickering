<?php return array(

  // API credentials ----------------------------------------------- /

  'api_key'    => 'foo',
  'api_secret' => 'bar',

  // Cache configuration ------------------------------------------- /

  'cache' => array(

    // Whether Flickering should cache requests or not
    'cache_requests' => false,

    // The lifetime of a cached request
    'lifetime'       => 60 * 24 * 365,
  ),

);