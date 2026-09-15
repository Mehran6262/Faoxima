<?php
$path = __DIR__ . '/bootstrap_trace.log';

header('Content-Type: text/plain; charset=utf-8');

if (!file_exists($path)) {
    http_response_code(404);
    echo "LOG_NOT_FOUND\n";
    echo "Expected: $path\n";
    exit;
}

echo file_get_contents($path);
