<?php
// PHPUnit bootstrap file

// Define constants used by the OpenCart system
if (!defined('DIR_LOGS')) {
    define('DIR_LOGS', sys_get_temp_dir() . '/oc_test_logs/');
}

if (!defined('DIR_CACHE')) {
    define('DIR_CACHE', sys_get_temp_dir() . '/oc_test_cache/');
}

if (!defined('DB_PREFIX')) {
    define('DB_PREFIX', 'oc_');
}

// Create temp directories
if (!is_dir(DIR_LOGS)) {
    mkdir(DIR_LOGS, 0777, true);
}

if (!is_dir(DIR_CACHE)) {
    mkdir(DIR_CACHE, 0777, true);
}

require_once __DIR__ . '/../vendor/autoload.php';
