<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

try {
    // پروژه‌ات را لود می‌کند تا BOT_TOKEN و اتصال DB آماده شود
    require_once dirname(__DIR__) . '/index.php';

    global $pdo, $db, $conn;

    $connection = $pdo ?? $db ?? $conn ?? null;
    if (!$connection instanceof PDO) {
        throw new RuntimeException('PDO connection was not found.');
    }

    $row = $connection
        ->query('SELECT webhook_secret_token FROM setting LIMIT 1')
        ->fetch(PDO::FETCH_ASSOC);

    $secret = trim((string)($row['webhook_secret_token'] ?? ''));
    if ($ ?? ''));
    if ($secret === '') {
        throw new_token is empty in DB.');
    }

    // مسیر وب‌هوک (اگر پروژه‌ات ورودی دیگری دارد، همین را تغییر بده)
    $webhookUrl = 'https://faoxima-1.onrender.com/index.php';

    $apiUrl = 'https://api.telegram.org/bot' . BOT_TOKEN . '/setWebhook?' . http_build_query([
        'url' => $webhookUrl,
        'secret_token' => $secret,
        'drop_pending_updates' => false,
    ]);

    $response = @file_get_contents($apiUrl);
    if ($response === false) {
        $err = error_get_last();
        throw new RuntimeException('Failed to call Telegram API: ' . ($err['message'] ?? 'unknown error'));
    }

    $decoded = json_decode($response, true);

    echo json_encode([
        'status' => (!empty($decoded['ok']) ? 'success' : 'error'),
        'telegram_ok' => $decoded['ok'] ?? null,
        'telegram_description' => $decoded['description'] ?? null,
        'webhook_url_set' => $webhookUrl,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
