rsync-lib
=========

A simple PHP rsync wrapper library

[![Build Status](https://secure.travis-ci.org/albertofem/rsync-lib.png?branch=master)](http://travis-ci.org/albertofem/rsync-lib) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/albertofem/rsync-lib/badges/quality-score.png?s=e6f2859cbe311a4bd952cdedd78ab0817e4e4c3d)](https://scrutinizer-ci.com/g/albertofem/rsync-lib/)

Installation
--------

Require it in composer:

    composer require albertofem/rsync-lib dev-master

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
        'host' => myhost.com, 
        'public_key' => '/my/key.pub'
    )
);

$rsync = new Rsync($config);

// change options programatically
$rsync->setFollowSymlinks(false);

$rsync->sync($origin, $target);
```