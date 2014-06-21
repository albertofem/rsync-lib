<?php

/*
 * This file is part of rsync-lib
 *
 * (c) Alberto Fernández <albertofem@gmail.com>
 *
 * For the full copyright and license information, please read
 * the LICENSE file that was distributed with this source code.
 */

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('AFM\Rsync\Tests', __DIR__);

/**
 * http://www.php.net/manual/en/function.rmdir.php#108113
 */
function rrmdir($dir)
{
	foreach(glob($dir . '/*') as $file)
	{
		if(is_dir($file))
		{
			rrmdir($file);
		}
		else
		{
			unlink($file);
		}
	}

	rmdir($dir);
}

function compare_directories($dir1, $dir2)
{
	$output = shell_exec("diff --brief " . $dir1 . " " . $dir2 . " 2>&1");

	if(strlen($output) > 0)
	{
		return false;
	}

	return true;
}