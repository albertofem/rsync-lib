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
        self::$sourceDir = __DIR__.'/dir1';
        self::$targetDir = __DIR__.'/dir2';

        @mkdir(self::$targetDir);
    }

    public static function tearDownAfterClass()
    {
        @rrmdir(self::$targetDir);
    }

    public function testValidExecutableLocation()
    {
        $rsync = new Rsync();
        $rsync->setExecutable("/usr/bin/rsync");

        $this->assertTrue(true);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidExecutableLocation()
    {
        $rsync = new Rsync();
        $rsync->setExecutable("/usr/not/exists/rsync!!");
    }

    public function testFollowSymlinkOptions()
    {
        $rsync = new Rsync(['follow_symlinks' => true]);

        $this->assertTrue($rsync->getFollowSymLinks());
    }

    public function testBasicSync()
    {
        $rsync = new Rsync();

        $rsync->sync($this->getSourceDir()."/*", $this->getTargetDir());

        $this->assertTrue(compare_directories($this->getSourceDir(), $this->getTargetDir()));
    }

    public function testRsyncWithSSHConnection()
    {
        $config = [
            'ssh' => [
                'username' => 'test',
                'host'     => 'test.com',
                'port'     => 2342,
            ],
        ];

        $rsync = new Rsync($config);

        $command = $rsync->getCommand(".", "/home/test/");

        $actual = $command->getCommand();
        $expected = "/usr/bin/rsync -La --rsh 'ssh -p '2342'' . test@test.com:/home/test/";

        $this->assertEquals($expected, $actual);

        $this->markTestIncomplete("Tested SSH connection string, but cannot test real SSH connection sync!");
    }

    public function testRsyncWithSingleExclude()
    {
        $rsync = new Rsync();
        $rsync->setExclude(['exclude1']);

        $expected = "/usr/bin/rsync -La --exclude 'exclude1' /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public function testRsyncWithMultipleExcludes()
    {
        $rsync = new Rsync();
        $rsync->setExclude(['exclude1', 'exclude2', 'exclude3']);

        $expected = "/usr/bin/rsync -La --exclude 'exclude1' --exclude 'exclude2' --exclude 'exclude3' /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public function testRsyncWithExcludeFrom()
    {
        $rsync = new Rsync();
        $rsync->setExcludeFrom('rsync_exclude.txt');

        $expected = "/usr/bin/rsync -La --exclude-from 'rsync_exclude.txt' /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public function testRsyncWithTimes()
    {
        $rsync = new Rsync();
        $rsync->setTimes(true);

        $expected = "/usr/bin/rsync -La --times /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
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
