#!/usr/bin/php5 -q
<?php

// Init scripts, library locations, and binaries
$script_dir = realpath(dirname(__FILE__));
$locum_lib_dir = substr($script_dir, 0, -21);

$locum_covers_config = parse_ini_file($locum_lib_dir . '/config/locum-covers.ini', true);

require_once($script_dir . '/locum-covers.php');

$locum = new locum_covers;

print_r($locum->process_covers(161687, 43000, 'NEW')); 

