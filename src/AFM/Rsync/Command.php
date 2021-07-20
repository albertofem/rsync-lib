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
 * Command abstraction class, construct commands
 * using arguments options and parameters
 *
 * Command format:
 * <pre>
 *        [executable] [-abLs](options) [-a value](argument) [--test value](argument) [parameter1] ... [parameterN]
 * </pre>
 *
 * @author Alberto <albertofem@gmail.com>
 */
class Command
{
    /**
     * @var string
     */
    private $executable;

    /**
     * @var array
     */
    private $options = array();

    /**
     * @var array
     */
    private $arguments = array();

    /**
     * @var string
     */
    private $command;

    /**
     * @var array
     */
    private $parameters = array();

    /**
     * @var int|null
     */
    private $exitCode;

    /**
     * @var string
     */
    private $stderr;

    /**
     * @var string
     */
    private $stdout;

    /**
     * Every command must have an executable
     *
     * @param $executable
     */
    public function __construct($executable)
    {
        $this->executable = $executable;
    }

    /**
     * Adds a parameter to the command, will be appended
     * in the same order as insertion at the end
     *
     * @param $parameter
     */
    public function addParameter($parameter)
    {
        $this->parameters[] = $parameter;
    }

    /**
     * Adds an option to the command, will be
     * appended to the command in the next format:
     *
     * <pre>
     *        -aBLs
     * </pre>
     *
     * @param $option
     */
    public function addOption($option)
    {
        $this->options[] = $option;
    }

    /**
     * Adds an argument to the command. If the argument
     * is more than one letter, "-- " will be appended before
     * if not, it will act as an option with a value:
     *
     * <pre>
     *        --argument [value]
     *        -p [value]
     * </pre>
     *
     * @param $name
     * @param bool|mixed $value
     */
    public function addArgument($name, $value = true)
    {
        $this->arguments[$name][] = $value;
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
     * Constructs the command appendind options,
     * arguments, executable and parameters
     *
     * @return string
     */
    protected function constructCommand()
    {
        $command = array();
        $command[] = $this->executable;

        if (!empty($this->options)) {
            $command[] = "-".implode($this->options);
        }

        foreach ($this->arguments as $argument => $values) {
            foreach ($values as $value) {
                if (strlen($argument) == 1) {
                    $command[] = "-".$argument." '".$value."'";
                } else {
                    $command[] = "--".(is_string($value) || is_int($value) ? $argument." '".$value."'" : $argument);
                }
            }
        }

        if (!empty($this->parameters)) {
            $command[] = implode(" ", $this->parameters);
        }

        $stringCommand = implode(" ", $command);

        return $stringCommand;
    }

    /**
     * Gets the command string
     *
     * @return mixed
     */
    public function getCommand()
    {
        if (is_null($this->command)) {
            $this->command = $this->constructCommand();
        }

        return $this->command;
    }

    /**
     * @see getCommand
     * @return mixed
     */
    public function __toString()
    {
        return $this->getCommand();
    }

    /**
     * Execute command, with optional output printer
     *
     * @param bool $showOutput
     */
    public function execute($showOutput = false)
    {
        $this->getCommand();

        if ($showOutput) {
            $this->executeWithOutput();
        } else {
            $this->executeWithoutOutput();
        }
    }

    /**
     * Execute and buffers command result to print it
     *
     * @throws \InvalidArgumentException When the command couldn't be executed
     */
    private function executeWithOutput()
    {
        if (($fp = popen($this->command, "r"))) {
            while (!feof($fp)) {
                echo fread($fp, 1024);
                flush();
            }

            fclose($fp);
        } else {
            throw new \InvalidArgumentException("Cannot execute command: '".$this->command."'");
        }
    }

    /**
     * Execute and save command result to property
     *
     * @throws \InvalidArgumentException When the command couldn't be executed
     */
    private function executeWithoutOutput()
    {
        $this->exitCode = 1;    // exit 0 on ok
        $this->stdout   = '';   // output of the command
        $this->stderr   = '';   // errors during execution

        $descriptor = [
            0 => ["pipe", "r"],    // stdin is a pipe that the child will read from
            1 => ["pipe", "w"],    // stdout is a pipe that the child will write to
            2 => ["pipe", "w"]     // stderr is a pipe
        ];

        $proc = proc_open($this->command, $descriptor, $pipes);

        if ($proc === false) {
            throw new \InvalidArgumentException("Cannot execute command: '".$this->command."'");
        }

        $this->stdout = trim(stream_get_contents($pipes[1]));
        $this->stderr = trim(stream_get_contents($pipes[2]));

        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $this->exitCode = proc_close($proc);

        return $this;
    }

    /**
     * Return the last command execution exitCode, null if command hasn't been executed yet
     *
     * @return string|null
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * Return the last command execution stderr, null if command hasn't been executed yet
     *
     * @return string|null
     */
    public function getStderr()
    {
        return $this->stderr;
    }

    /**
     * Return the last command execution stdout, null if command hasn't been executed yet
     *
     * @return string|null
     */
    public function getStdout()
    {
        return $this->stdout;
    }
}
