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

class Command
{
	private $executable;

	private $options = array();

	private $arguments = array();

	private $command;

	private $parameters = array();

	public function __construct($executable = "")
	{
		$this->executable = $executable;
	}

	public function addParameter($parameter)
	{
		$this->parameters[] = $parameter;
	}

	public function addOption($option)
	{
		$this->options[] = $option;
	}

	public function addArgument($name, $value = true)
	{
		$this->arguments[$name] = $value;
	}

	public function setExecutable($executable)
	{
		$this->executable = $executable;
	}

	public function getExecutable()
	{
		return $this->executable;
	}

	protected function constructCommand()
	{
		$command[] = $this->executable;

		if(!empty($this->options))
			$command[] = "-" . implode($this->options);

		foreach($this->arguments as $argument => $value)
		{
			if(strlen($argument) == 1)
			{
				$command[] = "-" . $argument . " '". $value. "'";
			}
			else
			{
				$command[] = "--" . (is_string($value) || is_int($value) ? $argument . " '" . $value. "'" : $argument);
			}
		}

		if(!empty($this->parameters))
			$command[] = implode(" ", $this->parameters);

		$stringCommand = implode(" ", $command);

		return $stringCommand;
	}

	public function getCommand()
	{
		if(is_null($this->command))
			$this->command = $this->constructCommand();

		return $this->command;
	}

	public function __toString()
	{
		return $this->getCommand();
	}

	public function execute($showOutput = false)
	{
		$this->getCommand();

		if($showOutput)
			$this->executeWithOutput();
		else
			shell_exec($this->command);
	}

	private function executeWithOutput()
	{
		if(($fp = popen($this->command, "r")))
		{
			while(!feof($fp))
			{
				echo fread($fp, 1024);
				flush();
			}

			fclose($fp);
		}
		else
		{
			throw new \InvalidArgumentException("Cannot execute command: '" .$this->command. "'");
		}
	}
}
