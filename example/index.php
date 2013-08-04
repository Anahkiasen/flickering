<?php
use Flickering\Facades\Flickering;
use Underscore\Types\String;

require '../vendor/autoload.php';

// Authentificate with the API ------------------------------------- /

Flickering::handshake();

// Create a basic router to handle Opauth authentification --------- /

// Get current request URI
$currentRequest = String::remove($_SERVER['REQUEST_URI'], '/flickering/example/');

// If we're on the login page, or just came back from it, let Opauth handle it
if ($currentRequest == '/flickr/' or String::startsWith($currentRequest, '/flickr/oauth_callback')) {
	return Flickering::getOpauth();
}

// If Opauth just took us to the callback adress, launch callback method
// to store user access token
if ($currentRequest == '/flickr/callback') {
	Flickering::getOpauthCallback();
}

// Display login link if not authentified
if (!Flickering::isAuthentified()) {
	echo '<a href="flickr/">Login to Flickr</a>';
	exit();
}

// Go crazy -------------------------------------------------------- /

$userUid = Flickering::getUser()->getUid();
$photos  = Flickering::peopleGetPhotos($userUid)->getResults('photo');

var_dump($photos->obtain());