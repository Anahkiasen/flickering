<?php return array(

  // Base paths
  'path'          => '/',
  'callback_url'  => '{path}callback.php',

  'security_salt' => 'LDFmfiilYf8Fyw5W10rx4W1KsVrieQCnpBzzpTBWA5vJidQKDx8pMJbmw28R1C4m',

  'strategy_dir'  => __DIR__.'/../vendor/flickr',

  // Strategies ---------------------------------------------------- /

  'Strategy' => array(
    'Flickr' => array(
      'api_id' => '30de750088fe29573e0fb8bcdefbd473',
      'api_secret' => 'c700f167e4f9a162',
    ),
  ),
);
