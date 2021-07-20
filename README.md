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

    composer require albertofem/rsync-lib 1.0.0

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

Used errors & output

```php
<?php

use AFM\Rsync\Rsync;

$origin = __DIR__;
$target = "/target/dir/";

$rsync = new Rsync;

$rsync->setShowOutput(false);

$command = $rsync->sync($origin, $target);

$result = $command->exitCode();
if (0 == $result) {
    print "Success!\nDetails:\n";
    print $command->getStdout() . "\n";
} else {
    print "Error No {$result}: " . $command->getStderr() . "\n";
}
```


Options
---------

| Construct options  | Rsync argument            | Comment                                          |
| ------------------ | ------------------------- | ------------------------------------------------ |
| executable         |                           | path of rsync (default: `/usr/bin/rsync`)        |
| archive            | -a, --archive             | archive mode; equals -rlptgoD (no -H,-A,-X)      |
| update             | -u, --update              | skip files that are newer on the receiver        |
| follow_symlinks    | -L, --copy-links          | transform symlink into referent file/dir         |
| dry_run            | -n, --dry-run             | perform a trial run with no changes made         |
| option_parameters  |                           | add any optional options we've specified         |
| verbose            | -v, --verbose             | increase verbosity                               |
| delete_from_target |     --delete              | delete extraneous files from destination dirs    |
| delete_excluded    |     --delete-excluded     | also delete excluded files from destination dirs |
| exclude            |     --exclude=PATTERN     | exclude files matching PATTERN                   |
| excludeFrom        |     --exclude-from=FILE   | read exclude patterns from FILE                  |
| recursive          | -r, --recursive           | recurse into directories                         |
| times              | -t, --times               | preserve modification times                      |
| show_output        |                           | execute and buffers command result to print it   |
| ssh                |                           | set ssh options                                  |
| compression        | -z, --compress            | compress file data during the transfer           |
| remote_origin      |                           | use ssh for origin path                          |
| remove_source      |     --remove-source-files | sender removes synchronized files (non-dirs)     |
| info               |     --info=FLAGS          | fine-grained informational verbosity             |
| compare_dest       |     --compare-dest=DIR    | also compare destination files relative to DIR   |
| prune_empty_dirs   | -m, --prune-empty-dirs    | prune empty directory chains from the file-list  |
