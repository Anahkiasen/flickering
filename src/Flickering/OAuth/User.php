<?php
/**
 * User
 *
 * An authentified User
 */
namespace Flickering\OAuth;

use Underscore\Types\Arrays;

class User extends Token
{
	/**
	 * The User UID
	 * @var string
	 */
	protected $uid;

	/**
	 * The User Flickr identity
	 * @var array
	 */
	protected $person;

	/**
	 * The User basic informations
	 * @var array
	 */
	protected $informations;

	/**
	 * Create new User from an OAuth response
	 *
	 * @param array $response
	 */
	public function __construct($response = null)
	{
		$response = (array) $response;

		// Flickr informations
		$this->uid          = Arrays::get($response, 'uid');
		$this->informations = Arrays::get($response, 'info');
		$this->person       = Arrays::get($response, 'raw');

		// OAuth credentials
		$this->key    = Arrays::get($response, 'credentials.token');
		$this->secret = Arrays::get($response, 'credentials.secret');
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////// PUBLIC INFORMATIONS ///////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the UID
	 *
	 * @return string
	 */
	public function getUid()
	{
		return $this->uid;
	}

	/**
	 * Get the base informations
	 *
	 * @return array
	 */
	public function getInformations()
	{
		return $this->informations;
	}

	/**
	 * Get the Flickr person
	 *
	 * @return array
	 */
	public function getPerson()
	{
		return $this->person;
	}
}
