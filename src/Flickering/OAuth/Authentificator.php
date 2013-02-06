<?php
namespace Flickering\OAuth;

use Flickering\Flickering;
use Flickering\Helpers;

class Authentificator
{
  private $consumer;

  /**
   * Start up authentification
   *
   * @param Flickering $flickering [description]
   */
  public function __construct(Flickering $flickering)
  {
    $this->flickering = $flickering;

  }

  /**
   * Get an authentification link to present to the user
   *
   * @return string
   */
  public function getAuthentificationUrl()
  {
    $config = $this->flickering->getConfig()->get('opauth');
    $config['Strategy']['Flickr']['key'] = $this->flickering->getApiKey();
    $config['Strategy']['Flickr']['secret'] = $this->flickering->getApiSecret();

    $opauth = new \Opauth($config);
  }

}