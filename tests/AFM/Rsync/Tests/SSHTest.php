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
		$fakePublicKey = __DIR__ . '/fake_key.pub';

		touch($fakePublicKey);

		new SSH(array('port' => 1443, 'public_key' => $fakePublicKey));

		$this->assertTrue(true);

		unlink($fakePublicKey);
	}

	/**
 	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidPublicKey()
	{
		new SSH(array('public_key' => '/cant/read!'));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testInvalidPortNumber()
	{
		new SSH(array('port' => 'not_a_number'));
	}
}
