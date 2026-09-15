<?php

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

if (!defined('REFACTORED_LEGACY_ROOT')) {
    define('REFACTORED_LEGACY_ROOT', dirname(__DIR__));
}
@chdir(REFACTORED_LEGACY_ROOT);
require __DIR__ . '/_error_log.php';
require __DIR__ . '/rx/index/index.php';

