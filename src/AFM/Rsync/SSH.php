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
 * Abstract SSH connection command. Note that if you
 * don't specify a public key, you will be prompted for
 * the remote server password
 *
 * @author Alberto <albertofem@gmail.com>
 */
class SSH extends AbstractProtocol
{
    /**
     * @var string
     */
    protected $executable = "ssh";

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port = 22;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var null
     */
    protected $privateKey = null;

    /**
     * Injects and validates config
     *
     * @param array $options
     */
    public function __construct(Array $options = array())
    {
        $this->setOption($options, 'executable', 'setExecutable');
        $this->setOption($options, 'host', 'setHost');
        $this->setOption($options, 'port', 'setPort');
        $this->setOption($options, 'username', 'setUsername');
        $this->setOption($options, 'private_key', 'setPrivateKey');
        $this->setOption($options, 'public_key', 'setPrivateKey');
    }

    /**
     * @param $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param $port
     *
     * @throws \InvalidArgumentException If the port is not numeric
     */
    public function setPort($port)
    {
        if (!is_int($port)) {
            throw new \InvalidArgumentException("SSH port must be an integer");
        }

        $this->port = $port;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param $privateKey
     * @throws \InvalidArgumentException
     */
    public function setPrivateKey($privateKey)
    {
        if (!is_readable($privateKey)) {
            throw new \InvalidArgumentException("SSH private key '".$privateKey."' is not readable");
        }

        $this->privateKey = $privateKey;
    }

    /**
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Gets commands for this SSH connection
     *
     * @param bool $hostConnection
     *
     * @return string
     *
     * @throws \InvalidArgumentException If you don't specify a SSH username or host
     */
    public function getCommand($hostConnection = true)
    {
        if (is_null($this->username)) {
            throw new \InvalidArgumentException("You must specify a SSH username");
        }

        if (is_null($this->host)) {
            throw new \InvalidArgumentException("You must specify a SSH host to connect");
        }

        $command = new Command($this->executable);

        if ($this->port != 22) {
            $command->addArgument("p", $this->port);
        }

        if (!is_null($this->privateKey)) {
            $command->addArgument("i", $this->privateKey);
        }

        if ($hostConnection) {
            $command->addParameter($this->getHostConnection());
        }

        return $command;
    }

    /**
     * Gets only connection options, without user@host string
     *
     * @return string
     */
    public function getConnectionOptions()
    {
        return (string)$this->getCommand(false);
    }

    /**
     * Gets only host connection, without the rest
     * of options
     *
     * @return string
     */
    public function getHostConnection()
    {
        return $this->username."@".$this->host;
    }

    /**
     * @param $executable
     */
    public function setExecutable($executable)
    {
        $this->executable = $executable;
    }

    /**
     * @return string
     */
    public function getExecutable()
    {
        return $this->executable;
    }

    /**
     * @deprecated
     */
    public function setPublicKey($privateKey)
    {
        $this->setPrivateKey($privateKey);
    }

    /**
     * @deprecated
     */
    public function getPublicKey()
    {
        return $this->getPrivateKey();
    }
}
