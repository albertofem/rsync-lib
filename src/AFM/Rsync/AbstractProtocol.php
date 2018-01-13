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
 * Abstract protocol
 *
 * @author Alberto <albertofem@gmail.com>
 */
abstract class AbstractProtocol
{
    /**
     * @var string
     */
    protected $executable = "";

    /**
     * Shortcut to set options from array config
     *
     * @param array $options
     * @param $name
     * @param $method
     */
    protected function setOption(Array $options, $name, $method)
    {
        if (isset($options[$name])) {
            $this->$method($options[$name]);
        }
    }

    /**
     * Sets rsync executable location, i.e.: /usr/bin/rsync
     *
     * @param $rsyncLocation
     *
     * @throws \InvalidArgumentException If the rsync location is not executable
     */
    public function setExecutable($rsyncLocation)
    {
        if (!is_executable($rsyncLocation)) {
            throw new \InvalidArgumentException("Rsync location '".$rsyncLocation."' is invalid");
        }

        $this->executable = $rsyncLocation;
    }
}
