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

abstract class AbstractProtocol
{
	protected function setOption(Array $options, $name, $method)
	{
		if(isset($options[$name]))
			$this->$method($options[$name]);
	}
}
