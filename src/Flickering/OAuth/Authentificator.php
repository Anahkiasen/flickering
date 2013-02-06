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
    $opauth = new \Opauth($config);
  }

}