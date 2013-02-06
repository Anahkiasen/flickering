<?php
namespace Flickering\OAuth;

use Eher\OAuth\Consumer;
use Eher\OAuth\HmacSha1;
use Eher\OAuth\Request;
use Eher\OAuth\Token;
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

    $this->setConsumer();
    $this->setRequestToken();
    $this->setAccesstoken();
  }

  /**
   * Create a Consumer model to use
   */
  private function setConsumer()
  {
    $this->consumer = new Consumer($this->flickering->getApiKey(), $this->flickering->getApiSecret());
  }

  /**
   * Get a Request Token
   */
  private function setRequestToken()
  {
    // Create base models
    $signatureMethod = new HmacSha1;
    $request = Request::from_consumer_and_token(
      $this->consumer, null, "GET",
      "http://www.flickr.com/services/oauth/request_token",
      array()
    );

    // Configure request
    $request->set_parameter('oauth_callback', 'http://localhost:8888/_github/flickering/');
    $request->sign_request($signatureMethod, $this->consumer, null);

    // Fetch Request Token
    $requestToken = Helpers::curl($request->to_url());
    $requestToken = Helpers::parseQueryString($requestToken);

    // Save tokens
    $this->requestToken = new Token($requestToken['oauth_token'], $requestToken['oauth_token_secret']);
    $this->flickering->getSession()->set('requestToken', $this->requestToken->key);
    $this->flickering->getSession()->set('requestTokenSecret', $this->requestToken->secret);
  }

  public function setAccessToken()
  {
    if (!$this->flickering->getSession()->has('requestToken')) return false;
    if (!isset($_GET['oauth_token'])) return false;

    $requestToken = $this->flickering->getSession()->get('requestToken');
    $requestTokenSecret = $this->flickering->getSession()->get('requestTokenSecret');
    $this->requestToken = new \Eher\OAuth\Token($requestToken, $requestTokenSecret);

    // Create base models
    $signatureMethod = new HmacSha1;
    $request = Request::from_consumer_and_token(
      $this->consumer, $this->requestToken, "GET",
      "http://www.flickr.com/services/oauth/access_token",
      array()
    );
    $request->set_parameter('oauth_token', $_GET['oauth_token']);
    $request->set_parameter('oauth_verifier', $_GET['oauth_verifier']);
    $request->sign_request($signatureMethod, $this->consumer, $this->requestToken);

    var_dump($request->to_url());
    $test = Helpers::curl($request->to_url());
    var_dump($test);

  }

  /**
   * Get an authentification link to present to the user
   *
   * @return string
   */
  public function getAuthentificationUrl()
  {
    return 'http://www.flickr.com/services/oauth/authorize?oauth_token='.$this->requestToken->key;
  }

}