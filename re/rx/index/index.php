<?php
require_once dirname(__DIR__, 2) . '/_error_log.php';

file_put_contents(__DIR__ . '/eval_trace.log', date('c') . " | A: entered rx/index/index.php\n", FILE_APPEND);

if (!defined('REFACTORED_LEGACY_ROOT')) {
    define('REFACTORED_LEGACY_ROOT', dirname(__DIR__, 3));
}

file_put_contents(__DIR__ . '/eval_trace.log', date('c') . " | B: root defined\n", FILE_APPEND);

@chdir(REFACTORED_LEGACY_ROOT);

file_put_contents(__DIR__ . '/eval_trace.log', date('c') . " | C: chdir done\n", FILE_APPEND);

$__rx_parts = require __DIR__ . '/manifest.php';

file_put_contents(__DIR__ . '/eval_trace.log', date('c') . " | D: manifest loaded | parts=" . json_encode($__rx_parts) . "\n", FILE_APPEND);

$__rx_code = '';
foreach ($__rx_parts as $__rx_part) {
    $__rx_path = __DIR__ . DIRECTORY_SEPARATOR . $__rx_part;

    file_put_contents(__DIR__ . '/eval_trace.log', date('c') . " | E: loading part | {$__rx_part}\n", FILE_APPEND);

    if (!is_file($__rx_path)) {
        file_put_contents(__DIR__ . '/eval_trace.log', date('c') . " | X: missing part | {$__rx_path}\n", FILE_APPEND);
        rx_log_event('RX_MISSING_PART', $__rx_path, ['module' => basename(__DIR__)]);
        throw new RuntimeException('Missing refactored part: ' . $__rx_path);
    }

    $__rx_raw = file_get_contents($__rx_path);
    $__rx_raw = preg_replace('/^<\?php(\r\n|\r|\n)/', '', $__rx_raw, 1);
    $__rx_code .= $__rx_raw;
}

file_put_contents(__DIR__ . '/eval_trace.log', date('c') . " | F: all parts merged | code_length=" . strlen($__rx_code) . "\n", FILE_APPEND);

unset($__rx_parts, $__rx_part, $__rx_path, $__rx_raw);

try {
    file_put_contents(__DIR__ . '/eval_trace.log', date('c') . " | G: before eval\n", FILE_APPEND);
// === Telegram webhook input bootstrap (BEFORE eval) ===
$raw = file_get_contents('php://input');
$update = json_decode($raw, true);
if (!is_array($update)) $update = [];

$message = $update['message'] ?? null;
$callback_query = $update['callback_query'] ?? null;
$chat_member = $update['chat_member'] ?? null;

// Common fields used by legacy bootstrap.php
$text = $message['text'] ?? '';
$data = $callback_query['data'] ?? '';

$from_id = $message['from']['id']
    ?? $callback_query['from']['id']
    ?? $chat_member['from']['id']
    ?? 0;

$Chat_type = $message['chat']['type']
    ?? ($callback_query['message']['chat']['type'] ?? null)
    ?? ($chat_member['chat']['type'] ?? null);

// optional but commonly used
$chat_id = $message['chat']['id']
    ?? ($callback_query['message']['chat']['id'] ?? ($chat_member['chat']['id'] ?? 0));

file_put_contents(
    __DIR__ . '/eval_trace.log',
    date('c') . " | INPUT: parsed | from_id={$from_id} | chat_type=" . ($Chat_type ?? 'NULL') .
    " | text_len=" . strlen((string)$text) . " | data_len=" . strlen((string)$data) . "\n",
    FILE_APPEND
);
// === END input bootstrap ===

    eval($__rx_code);

    file_put_contents(__DIR__ . '/eval_trace.log', date('c') . " | H: after eval\n", FILE_APPEND);
} catch (Throwable $__rx_throwable) {
    file_put_contents(
        __DIR__ . '/eval_trace.log',
        date('c') . " | Z: throwable | " .
        $__rx_throwable->getMessage() .
        " | class=" . get_class($__rx_throwable) .
        " | file=" . $__rx_throwable->getFile() .
        " | line=" . $__rx_throwable->getLine() . "\n",
        FILE_APPEND
    );

    rx_log_event('RX_EVAL_THROWABLE', $__rx_throwable->getMessage(), [
        'class' => get_class($__rx_throwable),
        'file'  => $__rx_throwable->getFile(),
        'line'  => $__rx_throwable->getLine(),
    ]);
    throw $__rx_throwable;
}

unset($__rx_code);
