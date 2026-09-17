<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once dirname(__DIR__) . '/index.php';

    $candidateNames = [];

    foreach (array_keys($GLOBALS) as $name) {
        $lower = strtolower($name);

        if (
            strpos($lower, 'token') !== false ||
            strpos($lower, 'bot') !== false ||
            strpos($lower, 'telegram') !== false ||
            strpos($lower, 'api') !== false
        ) {
            $candidateNames[] = $name;
        }
    }

    sort($candidateNames);

    echo json_encode([
        'status' => 'success',
        'candidate_global_names' => $candidateNames,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
