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
	protected $executable = "ssh";

	protected $host;

	protected $port = 22;

	protected $username;

	protected $publicKey = null;

	public function __construct(Array $options = array())
	{
		$this->setOption($options, 'host', 'setHost');
		$this->setOption($options, 'port', 'setPort');
		$this->setOption($options, 'username', 'setUsername');
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

	public function getConnectionString($hostConnection = true)
	{
		if(is_null($this->username))
			throw new \InvalidArgumentException("You must specify a SSH username");

		if(is_null($this->host))
			throw new \InvalidArgumentException("You must specify a SSH host to connect");

		$command = new Command;
		$command->setExecutable($this->executable);

		if($this->port != 22)
			$command->addArgument("p", $this->port);

		if(!is_null($this->publicKey))
			$command->addArgument("i", $this->publicKey);

		if($hostConnection)
			$command->addParameter($this->getHostConnection());

		return (string) $command;
	}

	public function getConnectionOptions()
	{
		return (string) $this->getConnectionString(false);
	}

	public function getHostConnection()
	{
		return $this->username . "@" . $this->host;
	}

	public function setExecutable($executable)
	{
		$this->executable = $executable;
	}

	public function getExecutable()
	{
		return $this->executable;
	}
}
