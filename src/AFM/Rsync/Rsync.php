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
 * Rsync wrapper
 *
 * @author Alberto Fernández <albertofem@gmail.com>
 */
class Rsync extends AbstractProtocol
{
	protected $executable = "/usr/bin/rsync";

	protected $followSymLinks = true;

	protected $dryRun = false;

	protected $optionalParameters = array();

	protected $verbose = false;

	protected $deleteFromTarget = false;

	protected $deleteExcluded = false;

	protected $exclude = array();

	protected $recursive = true;

	protected $showOutput = true;

	protected $ssh;

	public function __construct(Array $options = array())
	{
		$this->setOption($options, 'executable', 'setExecutable');
		$this->setOption($options, 'follow_symlinks', 'setFollowSymLinks');
		$this->setOption($options, 'dry_run', 'setDryRun');
		$this->setOption($options, 'option_parameters', 'setOptionalParameters');
		$this->setOption($options, 'verbose', 'setVerbose');
		$this->setOption($options, 'delete_from_target', 'setDeleteFromTarget');
		$this->setOption($options, 'delete_excluded', 'setDeleteExcluded');
		$this->setOption($options, 'exclude', 'setExclude');
		$this->setOption($options, 'recursive', 'setRecursive');
		$this->setOption($options, 'show_output', 'setShowOutput');
		$this->setOption($options, 'ssh', 'setSshOptions');
	}

	public function setSshOptions($options)
	{
		if(is_null($this->ssh))
			$this->ssh = new SSH($options);
	}

	public function sync($origin, $target)
	{
		$command = $this->getCommand($origin, $target);

		$command->execute($this->showOutput);
	}

	public function setExecutable($rsyncLocation)
	{
		if(!is_executable($rsyncLocation))
			throw new \InvalidArgumentException("Rsync location '". $rsyncLocation. "' is invalid");

		$this->executable = $rsyncLocation;
	}

	public function getExecutable()
	{
		return $this->executable;
	}

	public function setFollowSymLinks($followSymLinks)
	{
		$this->followSymLinks = $followSymLinks;
	}

	public function getFollowSymLinks()
	{
		return $this->followSymLinks;
	}

	public function setDryRun($dryRun)
	{
		$this->dryRun = $dryRun;
	}

	public function getDryRun()
	{
		return $this->dryRun;
	}

	public function setOptionalParameters($optionalParameters)
	{
		$this->optionalParameters = $optionalParameters;
	}

	public function getOptionalParameters()
	{
		return $this->optionalParameters;
	}

	public function setVerbose($verbose)
	{
		$this->verbose = $verbose;
	}

	public function getVerbose()
	{
		return $this->verbose;
	}

	public function setDeleteExcluded($deleteExcluded)
	{
		$this->deleteExcluded = $deleteExcluded;
	}

	public function getDeleteExcluded()
	{
		return $this->deleteExcluded;
	}

	public function setDeleteFromTarget($deleteFromTarget)
	{
		$this->deleteFromTarget = $deleteFromTarget;
	}

	public function getDeleteFromTarget()
	{
		return $this->deleteFromTarget;
	}

	public function setExclude($exclude)
	{
		$this->exclude = $exclude;
	}

	public function getExclude()
	{
		return $this->exclude;
	}

	public function setRecursive($recursive)
	{
		$this->recursive = $recursive;
	}

	public function getRecursive()
	{
		return $this->recursive;
	}

	public function setShowOutput($showOutput)
	{
		$this->showOutput = $showOutput;
	}

	public function getShowOutput()
	{
		return $this->showOutput;
	}

	public function getCommand($origin, $target)
	{
		$command = new Command($this->executable);

		if($this->followSymLinks)
			$command->addOption("L");

		if($this->dryRun)
			$command->addOption("n");

		if($this->verbose)
			$command->addOption("v");

		if($this->deleteFromTarget)
			$command->addArgument('delete');

		if($this->deleteExcluded)
			$command->addArgument('delete-excluded');

		if(!empty($this->exclude))
		{
			foreach($this->exclude as $excluded)
			{
				$command->addArgument('exclude', $excluded);
			}
		}

		if($this->recursive)
			$command->addOption("a");

		$command->addParameter($origin);
		$command->addParameter($target);

		return $command;
	}
}
