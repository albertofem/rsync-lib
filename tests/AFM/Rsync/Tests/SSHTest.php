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

use AFM\Rsync\SSH;

class SSHTest extends \PHPUnit_Framework_TestCase
{
    public function testValidConfiguration()
    {
        $fakePrivateKey = __DIR__.'/fake_key';

        touch($fakePrivateKey);

        new SSH(array('port' => 1443, 'private_key' => $fakePrivateKey));

        $this->assertTrue(true);

        unlink($fakePrivateKey);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPrivateKey()
    {
        new SSH(array('private_key' => '/cant/read!'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPortNumber()
    {
        new SSH(array('port' => 'not_a_number'));
    }

    public function testGetConnectionString()
    {
        $ssh = new SSH(array('username' => 'test', 'host' => 'test.com'));

        $actual = $ssh->getCommand();
        $expected = "ssh test@test.com";

        $this->assertEquals($expected, $actual);
    }

    public function testGetConnectionNonStandardPort()
    {
        $ssh = new SSH(array('username' => 'test', 'host' => 'test.com', 'port' => 231));

        $actual = $ssh->getCommand();
        $expected = "ssh -p '231' test@test.com";

        $this->assertEquals($expected, $actual);
    }

    public function testGetConnectionWithPrivateKey()
    {
        $privateKey = "./key";
        $privateKeyWithSpaces = "./key key";

        touch($privateKey);
        touch($privateKeyWithSpaces);

        $ssh = new SSH(array('username' => 'test', 'host' => 'test.com', 'private_key' => $privateKey));

        $actual = $ssh->getCommand();
        $expected = "ssh -i '".$privateKey."' test@test.com";

        $this->assertEquals($expected, $actual);

        $ssh->setPrivateKey($privateKeyWithSpaces);

        $actual = $ssh->getCommand();
        $expected = "ssh -i '".$privateKeyWithSpaces."' test@test.com";

        $this->assertEquals($expected, $actual);

        unlink($privateKey);
        unlink($privateKeyWithSpaces);
    }

    public function testGetHostConnection()
    {
        $ssh = new SSH(array('username' => 'test', 'host' => 'test.com'));

        $actual = $ssh->getHostConnection();
        $expected = "test@test.com";

        $this->assertEquals($expected, $actual);
    }

    public function testGetConnectionOptions()
    {
        $ssh = new SSH(array('username' => 'test', 'host' => 'test.com', 'port' => 231, 'private_key' => '/dev/null'));

        $actual = $ssh->getConnectionOptions();
        $expected = "ssh -p '231' -i '/dev/null'";

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetConnectionNoUsername()
    {
        $ssh = new SSH;

        $ssh->getCommand();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetConnectionNoHost()
    {
        $ssh = new SSH(array('username' => 'test'));

        $ssh->getCommand();
    }

    public function testSetExecutable()
    {
        $ssh = new SSH(
            array('username' => 'test', 'host' => 'test.com', 'port' => 231, 'executable' => 'c:/cygwin/bin/ssh.exe')
        );

        $actual = $ssh->getConnectionOptions();
        $expected = "c:/cygwin/bin/ssh.exe -p '231'";

        $this->assertEquals($expected, $actual);
    }
}

