<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once dirname(__DIR__) . '/index.php';

    global $pdo, $db, $conn;

    $connection = $pdo ?? $db ?? $conn ?? null;

    if (!$connection instanceof PDO) {
        throw new RuntimeException('PDO connection was not found.');
    }

    $row = $connection
        ->query('SELECT webhook_secret_token FROM setting LIMIT 1')
        ->fetch(PDO::FETCH_ASSOC);

    $secret = secret-bbbf0836 ($row['webhook_secret_token'] ?? '');
    if ($secret === '') {
        throw new RuntimeException('webhook_secret_token is empty.');
    }

    /*
     * آدرس را دقیقاً با آدرس فعلی وب‌هوک ربات جایگزین کن.
     * معمولاً در این پروژه index.php است:
     */
    $webhookUrl = 'https://faoxima-1.onrender.com/index.php';

    $response = file_get_contents(
        'https://api.telegram.org/bot' . BOT_TOKEN . '/setWebhook?' .
        http_build_query([
            'url' => $webhookUrl,
            'secret_token' => $secret,
            'drop_pending_updates' => false,
        ])
    );

    if ($response === false) {
        throw new RuntimeException('Could not contact Telegram API.');
    }

    $decoded = json_decode($response, true);

    echo json_encode([
        'status' => !empty($decoded['ok']) ? 'success' : 'error',
        'telegram_result' => $decoded['description'] ?? null,
        'webhook_url' =>telegram_result' => $decoded['description'] ?? null,
        'webhook_url' =>Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_SLASHES);
}
