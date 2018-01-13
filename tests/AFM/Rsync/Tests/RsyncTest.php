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

        $rsync->sync($this->getSourceDir()."/*", $this->getTargetDir());

        $this->assertTrue(compare_directories($this->getSourceDir(), $this->getTargetDir()));
    }

    public function testRsyncWithSSHConnection()
    {
        $user = getenv('USER') ?: 'test_no_ssh_server';
        $targetBaseDir = getenv('HOME') ?: '/home';

        $config = array(
            'ssh' => array(
                'username' => $user,
                'host' => 'localhost',
                'port' => 2222,
            ),
        );

        $rsync = new Rsync($config);

        $command = $rsync->getCommand(".", $targetBaseDir."/test/");

        $actual = $command->getCommand();
        $expected = "/usr/bin/rsync -La --rsh 'ssh -p '2222'' . ".$user."@localhost:".$targetBaseDir."/test/";

        $this->assertEquals($expected, $actual);
    }

    public function testRsyncWithRealSSHConnection()
    {
        $user = getenv('USER');
        $targetBaseDir = getenv('HOME');
        $sshKey = $targetBaseDir.'/.ssh/id_rsa_rsync_test';

        if (!file_exists($sshKey)) {
            $this->markTestIncomplete('Cannot perform real SSH rsync due to missing SSH configuration');
        }

        $config = array(
            'ssh' => array(
                'username' => $user,
                'host' => 'localhost',
                'port' => 2222,
                'private_key' => $sshKey,
            ),
        );

        $rsync = new Rsync($config);
        $rsync->sync($this->getSourceDir()."/*", $this->getTargetDir());

        $this->assertTrue(compare_directories($this->getSourceDir(), $this->getTargetDir()));
    }

    public
    function testRsyncWithSingleExclude()
    {
        $rsync = new Rsync();
        $rsync->setExclude(array('exclude1'));

        $expected = "/usr/bin/rsync -La --exclude 'exclude1' /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public
    function testRsyncWithMultipleExcludes()
    {
        $rsync = new Rsync();
        $rsync->setExclude(array('exclude1', 'exclude2', 'exclude3'));

        $expected = "/usr/bin/rsync -La --exclude 'exclude1' --exclude 'exclude2' --exclude 'exclude3' /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public
    function testRsyncWithExcludeFrom()
    {
        $rsync = new Rsync();
        $rsync->setExcludeFrom('rsync_exclude.txt');

        $expected = "/usr/bin/rsync -La --exclude-from 'rsync_exclude.txt' /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public
    function testRsyncWithTimes()
    {
        $rsync = new Rsync();
        $rsync->setTimes(true);

        $expected = "/usr/bin/rsync -La --times /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public
    function testRsyncWithCompression()
    {
        $rsync = new Rsync();
        $rsync->setCompression(true);

        $expected = "/usr/bin/rsync -Lza /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public
    function testRsyncWithOptionalParametersArray()
    {
        $rsync = new Rsync();
        $rsync->setOptionalParameters(array('z', 'p'));

        $expected = "/usr/bin/rsync -Lzpa /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public
    function testRsyncWithOptionalParametersString()
    {
        $rsync = new Rsync();
        $rsync->setOptionalParameters('zp');

        $expected = "/usr/bin/rsync -Lzpa /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public
    function testRsyncWithInfo()
    {
        $rsync = new Rsync();
        $rsync->setInfo('all0');

        $expected = "/usr/bin/rsync -La --info 'all0' /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public
    function testRsyncWithCompareDest()
    {
        $rsync = new Rsync();
        $rsync->setCompareDest('/Path/To/File');

        $expected = "/usr/bin/rsync -La --compare-dest '/Path/To/File' /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public
    function testRsyncWithRemoveSourceFile()
    {
        $rsync = new Rsync();
        $rsync->setRemoveSource(true);

        $expected = "/usr/bin/rsync -La --remove-source-files /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public
    function testRsyncWithPruneEmptyDIrs()
    {
        $rsync = new Rsync();
        $rsync->setPruneEmptyDirs(true);

        $expected = "/usr/bin/rsync -La --prune-empty-dirs /origin /target";
        $actual = $rsync->getCommand('/origin', '/target')->getCommand();

        $this->assertEquals($expected, $actual);
    }

    public
    function getTargetDir()
    {
        return self::$targetDir;
    }

    public
    function getSourceDir()
    {
        return self::$sourceDir;
    }
}
