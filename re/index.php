<?php

file_put_contents(__DIR__ . '/trace.log', "A: start\n", FILE_APPEND);

ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/php_error.log');
error_reporting(E_ALL);

file_put_contents(
    __DIR__ . '/webhook_debug.log',
    date('Y-m-d H:i:s') .
    " | METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? '') .
    " | URI: " . ($_SERVER['REQUEST_URI'] ?? '') .
    " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? '') .
    " | INPUT: " . file_get_contents('php://input') .
    PHP_EOL,
    FILE_APPEND
);

file_put_contents(__DIR__ . '/trace.log', "B: before define root\n", FILE_APPEND);

if (!defined('REFACTORED_LEGACY_ROOT')) {
    define('REFACTORED_LEGACY_ROOT', dirname(__DIR__));
}

file_put_contents(__DIR__ . '/trace.log', "C: after define root\n", FILE_APPEND);

@chdir(REFACTORED_LEGACY_ROOT);

file_put_contents(__DIR__ . '/trace.log', "D: after chdir\n", FILE_APPEND);

require __DIR__ . '/_error_log.php';

file_put_contents(__DIR__ . '/trace.log', "E: after _error_log.php\n", FILE_APPEND);

require __DIR__ . '/rx/index/index.php';

file_put_contents(__DIR__ . '/trace.log', "F: after rx/index/index.php\n", FILE_APPEND);
