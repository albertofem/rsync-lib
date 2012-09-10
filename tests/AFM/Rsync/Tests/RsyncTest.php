<?php

/*
 * This file is part of rsync-lib
 *
 * (c) Alberto Fernández <albertofem@gmail.com>
 *
 * For the full copyright and license information, please read
 * the LICENSE file that was distributed with this source code.
 */

namespace AFM\Rsync\Tests;

use AFM\Rsync\Rsync;

class RsyncTest extends \PHPUnit_Framework_TestCase
{
	private static $targetDir;

	private static $sourceDir;

	public function setUp()
	{
		@rrmdir(self::$targetDir);
	}

	public static function setUpBeforeClass()
	{
		self::$sourceDir = __DIR__ . '/dir1';
		self::$targetDir = __DIR__ . '/dir2';

		@mkdir(self::$targetDir);
	}

	public static function tearDownAfterClass()
	{
		@rrmdir(self::$targetDir);
	}

	public function testValidExecutableLocation()
	{
		$rsync = new Rsync;
		$rsync->setExecutable("/usr/bin/rsync");

		$this->assertTrue(true);
	}

	/**
 	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidExecutableLocation()
	{
		$rsync = new Rsync;
		$rsync->setExecutable("/usr/not/exists/rsync!!");
	}

	public function testFollowSymlinkOptions()
	{
		$rsync = new Rsync(array('follow_symlinks' => true));

		$this->assertTrue($rsync->getFollowSymLinks());
	}

	public function testBasicSync()
	{
		$rsync = new Rsync;

		$rsync->sync($this->getSourceDir() . "/*", $this->getTargetDir());

		$this->assertTrue(compare_directories($this->getSourceDir(), $this->getTargetDir()));
	}

	public function getTargetDir()
	{
		return self::$targetDir;
	}

	public function getSourceDir()
	{
		return self::$sourceDir;
	}
}
