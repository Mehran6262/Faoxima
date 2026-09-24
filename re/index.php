<?php

// Redirect PHP errors and diagnostics to the Render live logs.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', 'php://stderr');
error_reporting(E_ALL);

$webhookRawBody = file_get_contents('php://input');

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

error_log(
    '[WEBHOOK-DIAG] ' .
    'method=' . $requestMethod .
    ' uri=' . $requestUri .
    ' content_type=' . $contentType .
    ' body_length=' . strlen($webhookRawBody) .
    ' user_agent=' . substr($userAgent, 0, 160)
);

if ($webhookRawBody !== '') {
    $decodedWebhook = json_decode($webhookRawBody, true);

    error_log(
        '[WEBHOOK-DIAG] ' .
        'json_valid=' . (json_last_error() === JSON_ERROR_NONE ? 'yes' : 'no') .
        ' update_id=' . (
            is_array($decodedWebhook) && isset($decodedWebhook['update_id'])
                ? (string) $decodedWebhook['update_id']
                : 'none'
        ) .
        ' has_message=' . (
            is_array($decodedWebhook) && isset($decodedWebhook['message'])
                ? 'yes'
                : 'no'
        ) .
        ' has_callback=' . (
            is_array($decodedWebhook) && isset($decodedWebhook['callback_query'])
                ? 'yes'
                : 'no'
        )
    );
} else {
    error_log('[WEBHOOK-DIAG] empty request body');
}

// Make the already-read request body available to the next included file.
$GLOBALS['WEBHOOK_RAW_BODY'] = $webhookRawBody;

error_log('>>> [1] re/index.php STARTED');

if (!defined('REFACTORED_LEGACY_ROOT')) {
    define('REFACTORED_LEGACY_ROOT', dirname(__DIR__));
}

@chdir(REFACTORED_LEGACY_ROOT);

error_log(
    '>>> [2] Root directory set to: ' . REFACTORED_LEGACY_ROOT
);

// Load the error logger if it exists.
$errorLogFile = __DIR__ . '/_error_log.php';

if (is_file($errorLogFile)) {
    error_log('>>> [3] Loading _error_log.php');
    require_once $errorLogFile;
} else {
    error_log('>>> [3] _error_log.php NOT FOUND');
}

// Load the webhook processing file.
$targetFile = __DIR__ . '/rx/index/index.php';

if (!is_file($targetFile)) {
    error_log('[RX-DIAG] FILE NOT FOUND: ' . $targetFile);
    http_response_code(500);
    exit;
}

try {
    error_log('[RX-DIAG] BEFORE require: ' . $targetFile);

    require_once $targetFile;

    error_log('[RX-DIAG] AFTER require: completed successfully');
} catch (\Throwable $e) {
    error_log(
        '[RX-DIAG] ' .
        get_class($e) .
        ': ' . $e->getMessage() .
        ' | FILE: ' . $e->getFile() .
        ' | LINE: ' . $e->getLine()
    );

    throw $e;
}
