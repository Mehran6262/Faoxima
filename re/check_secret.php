<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

try {
    // فایل check_secret.php داخل re است و index.php در ریشه پروژه قرار دارد.
    require_once dirname(__DIR__) . '/index.php';

    global $pdo, $db, $conn;

    // نام‌های محتمل اتصال در پروژه را بررسی می‌کنیم.
    $connection = $pdo ?? $db ?? $conn ?? null;

    if (!$connection instanceof PDO) {
        throw new RuntimeException(
            'PDO connection was not found. Available globals: ' .
            implode(', ', array_keys($GLOBALS))
        );
    }

    $row = $connection
        ->query('SELECT webhook_secret_token FROM setting LIMIT 1')
        ->fetch(PDO::FETCH_ASSOC);

    $secret = (string) ($row['webhook_secret_token'] ?? '');

    echo json_encode([
        'status' => 'success',
        'has_secret' => $secret !== '',
        'secret_length' => strlen($secret),
        'secret_prefix' => $secret !== '' ? substr($secret, 0, 3) . '***' : null,
    ], JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_SLASHES);
}
