<?php
/**
 * Token
 *
 * A security token
 */
namespace Flickering\OAuth;

abstract class Token
{
	/**
	 * His token
	 * @var string
	 */
	protected $key;

	/**
	 * His secret
	 * @var string
	 */
	protected $secret;

	/**
	 * Create new User
	 *
	 * @param string $key
	 * @param string $secret
	 */
	public function __construct($key, $secret)
	{
		$this->key    = $key;
		$this->secret = $secret;
	}

	/**
	 * Get token
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Get secret key
	 *
	 * @return string
	 */
	public function getSecret()
	{
		return $this->secret;
	}
}
