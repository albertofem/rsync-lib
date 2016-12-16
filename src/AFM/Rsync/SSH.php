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
    protected $publicKey = null;

    /**
     * @var string
     */
    protected $strictHostKeyCheking = null;

    /**
     * @var string
     */
    protected $userKnownHostsFile = null;

    /**
     * Injects and validates config
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setOption($options, 'executable', 'setExecutable');
        $this->setOption($options, 'host', 'setHost');
        $this->setOption($options, 'port', 'setPort');
        $this->setOption($options, 'username', 'setUsername');
        $this->setOption($options, 'public_key', 'setPublicKey');
        $this->setOption($options, 'strict_host_key_checking', 'setStrictHostKeyChecking');
        $this->setOption($options, 'user_known_hosts_file', 'setUserKnownHostFile');
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
     * @param $publicKey
     * @throws \InvalidArgumentException
     */
    public function setPublicKey($publicKey)
    {
        if (!is_readable($publicKey)) {
            throw new \InvalidArgumentException("SSH public key '" . $publicKey . "' is not readable");
        }

        $this->publicKey = $publicKey;
    }

    /**
     * @return null
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $string
     * @throws \InvalidArgumentException
     */
    public function setStrictHostKeyChecking($strictHostKeyCheking)
    {
        //dd($strictHostKeyCheking);
        if ($strictHostKeyCheking !== 'yes' && $strictHostKeyCheking !== 'no') {
            throw new \InvalidArgumentException("StrictHostKeyCheking must be set to 'yes' or 'no'");
        }
        $this->strictHostKeyCheking = "StrictHostKeyChecking={$strictHostKeyCheking}";
    }

    /**
     * @return $string
     */
    public function getStrictHostKeyChecking($strictHostKeyCheking)
    {
        return $this->strictHostKeyCheking;
    }

    /**
     * @param $string
     */
    public function setUserKnownHostFile($userKnownHostsFile)
    {
        $this->userKnownHostsFile = "UserKnownHostsFile={$userKnownHostsFile}";
    }

    /**
     * @return $string
     */
    public function getUserKnownHostFile($userKnownHostFile)
    {
        return $this->userKnownHostFile;
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

        if (!is_null($this->publicKey)) {
            $command->addArgument("i", $this->publicKey);
        }

        if (!is_null($this->strictHostKeyCheking)) {
            $command->addArgument("o", $this->strictHostKeyCheking);
        }

        if (!is_null($this->userKnownHostsFile)) {
            $command->addArgument("o", $this->userKnownHostsFile);
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
        return (string) $this->getCommand(false);
    }

    /**
     * Gets only host connection, without the rest
     * of options
     *
     * @return string
     */
    public function getHostConnection()
    {
        return $this->username . "@" . $this->host;
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
}
