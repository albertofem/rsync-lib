<?php

/*
 * This file is part of rsync-lib
 *
 * (c) Alberto Fernández <albertofem@gmail.com>
 *
 * For the full copyright and license information, please read
 * the LICENSE file that was distributed with this source code.
 */

namespace AFM\Rsync;

/**
 * SSH protocol abstraction
 *
 * @author Alberto <albertofem@gmail.com>
 */
class SSH extends AbstractProtocol
{
	protected $host;

	protected $port = 22;

	protected $username;

	protected $password;

	protected $publicKey = null;

	public function __construct(Array $options = array())
	{
		$this->setOption($options, 'host', 'setHost');
		$this->setOption($options, 'port', 'setPort');
		$this->setOption($options, 'username', 'serUsername');
		$this->setOption($options, 'password', 'setPassword');
		$this->setOption($options, 'public_key', 'setPublicKey');
	}

	public function setHost($host)
	{
		$this->host = $host;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function setPassword($password)
	{
		$this->password = $password;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function setPort($port)
	{
		if(!is_numeric($port))
			throw new \InvalidArgumentException("SSH port must be numeric");

		$this->port = $port;
	}

	public function getPort()
	{
		return $this->port;
	}

	public function setPublicKey($publicKey)
	{
		if(!is_readable($publicKey))
			throw new \InvalidArgumentException("SSH public key '" .$publicKey. "' is not readable");

		$this->publicKey = $publicKey;
	}

	public function getPublicKey()
	{
		return $this->publicKey;
	}

	public function setUsername($username)
	{
		$this->username = $username;
	}

	public function getUsername()
	{
		return $this->username;
	}
}
