rsync-lib
=========

A simple PHP rsync wrapper library

[![Build Status](https://secure.travis-ci.org/albertofem/rsync-lib.png?branch=master)](http://travis-ci.org/albertofem/rsync-lib) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/albertofem/rsync-lib/badges/quality-score.png?s=e6f2859cbe311a4bd952cdedd78ab0817e4e4c3d)](https://scrutinizer-ci.com/g/albertofem/rsync-lib/)

Requirements
----

This library requires PHP >=5.4

Changelog
----

01-13-2018

* Dropped PHP 5.3 support
* Rename `public_key` option to the correct `private_key` one. Old one still works and will be deprecated in version 2.0

Installation
--------

Require it in composer:

    composer require albertofem/rsync-lib ~1.0

Install it:

    composer update albertofem/rsync-lib

If you want to run the tests:

    ./vendor/bin/phpunit

Usage
---------

Basic usage example:

```php
<?php

use AFM\Rsync\Rsync;

$origin = __DIR__;
$target = "/target/dir/";

$rsync = new Rsync;
$rsync->sync($origin, $target);
```

Change behaviour:

```php
<?php

use AFM\Rsync\Rsync;

$origin = __DIR__;
$target = "/target/dir";

$config = array(
    'delete_from_target' => true, 
    'ssh' => array(
        'host' => 'myhost.com', 
        'private_key' => '/my/key'
    )
);

$rsync = new Rsync($config);

// change options programatically
$rsync->setFollowSymlinks(false);

$rsync->sync($origin, $target);
```