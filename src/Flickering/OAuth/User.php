<?php
/**
 * User
 *
 * An authentified User
 */
namespace Flickering\OAuth;

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
		$this->uid          = array_get($response, 'uid');
		$this->informations = array_get($response, 'info');
		$this->person       = array_get($response, 'raw');

		// OAuth credentials
		$this->key    = array_get($response, 'credentials.token');
		$this->secret = array_get($response, 'credentials.secret');
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
