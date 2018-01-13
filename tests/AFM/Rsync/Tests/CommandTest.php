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

use AFM\Rsync\Command;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandWithOnlyOptions()
    {
        $command = new Command("test");

        $command->addOption("a");

        $actual = $command->getCommand();
        $expected = "test -a";

        $this->assertEquals($expected, $actual);
    }

    public function testCommandWithMultipleOptions()
    {
        $command = new Command("test");

        $command->addOption("a");
        $command->addOption("b");
        $command->addOption("z");

        $actual = $command->getCommand();
        $expected = "test -abz";

        $this->assertEquals($expected, $actual);
    }

    public function testCommandWithOnlyOneParameter()
    {
        $command = new Command("test");

        $command->addParameter("test");

        $actual = $command->getCommand();
        $expected = "test test";

        $this->assertEquals($expected, $actual);
    }

    public function testCommandWithOneOptionAndOneParameter()
    {
        $command = new Command("test");

        $command->addOption("a");
        $command->addParameter("test");

        $actual = $command->getCommand();
        $expected = "test -a test";

        $this->assertEquals($expected, $actual);
    }

    public function testCommandWithOneOptionAndMultipleParameters()
    {
        $command = new Command("test");

        $command->addOption("a");
        $command->addParameter("test");
        $command->addParameter("test2");

        $actual = $command->getCommand();
        $expected = "test -a test test2";

        $this->assertEquals($expected, $actual);
    }

    public function testCommandWithOnlyOneSimpleArgument()
    {
        $command = new Command("test");

        $command->addArgument("test");

        $actual = $command->getCommand();
        $expected = "test --test";

        $this->assertEquals($expected, $actual);
    }

    public function testCommandWithOnlyOneArgument()
    {
        $command = new Command("test");

        $command->addArgument("test", "value");

        $actual = $command->getCommand();
        $expected = "test --test 'value'";

        $this->assertEquals($expected, $actual);
    }
}
