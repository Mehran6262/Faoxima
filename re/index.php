<?php

// هدایت خطاها و ردپاها به کنسول زنده رندر
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', 'php://stderr');
error_reporting(E_ALL);

error_log('>>> [1] re/index.php STARTED');

if (!defined('REFACTORED_LEGACY_ROOT')) {
    define('REFACTORED_LEGACY_ROOT', dirname(__DIR__));
}

@chdir(REFACTORED_LEGACY_ROOT);
error_log('>>> [2] Root directory set to: ' . REFACTORED_LEGACY_ROOT);

// بررسی وجود فایل _error_log.php
if (is_file(__DIR__ . '/_error_log.php')) {
    error_log('>>> [3] Loading _error_log.php');
    require_once __DIR__ . '/_error_log.php';
} else {
    error_log('>>> [3] _error_log.php NOT FOUND');
}

// بررسی وجود و اجرای فایل مرحله بعد
$targetFile = __DIR__ . '/rx/index/index.php';

if (is_file($targetFile)) {
    try {
        error_log(
            "[RX-DIAG] BEFORE require: " . $targetFile . "\n",
            3,
            'php://stderr'
        );

        require_once $targetFile;

        error_log(
            "[RX-DIAG] AFTER require: completed successfully\n",
            3,
            'php://stderr'
        );
    } catch (\Throwable $e) {
        error_log(
            "[RX-DIAG] "
            . get_class($e) . ": "
            . $e->getMessage()
            . " | FILE: " . $e->getFile()
            . " | LINE: " . $e->getLine()
            . "\n",
            3,
            'php://stderr'
        );

        throw $e;
    }
} else {
    error_log(
        "[RX-DIAG] FILE NOT FOUND: " . $targetFile . "\n",
        3,
        'php://stderr'
    );
}
