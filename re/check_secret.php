<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once dirname(__DIR__) . '/index.php';

    global $pdo, $db, $conn;

    $connection = null;

    if (isset($pdo) && $pdo instanceof PDO) {
        $connection = $pdo;
    } elseif (isset($db) && $db instanceof PDO) {
        $connection = $db;
    } elseif (isset($conn) && $conn instanceof PDO) {
        $connection = $conn;
    }

    if (!$connection instanceof PDO) {
        throw new RuntimeException('PDO connection was not found.');
    }

    $result = $connection->query(
        'SELECT webhook_secret_token FROM setting LIMIT 1'
    );

    if ($result === false) {
        throw new RuntimeException('Database query failed.');
    }

    $row = $result->fetch(PDO::FETCH_ASSOC);
    $secret = '';

    if (is_array($row) && isset($row['webhook_secret_token'])) {
        $secret = (string) $row['webhook_secret_token'];
    }

    if ($secret === '') {
        throw new RuntimeException('webhook_secret_token is empty in DB.');
    }

    if (!defined('BOT_TOKEN') || BOT_TOKEN === '') {
        throw new RuntimeException('BOT_TOKEN is not defined after loading index.php.');
    }

    $webhookUrl = 'https://faoxima-1.onrender.com/index.php';

    $params = [
        'url' => $webhookUrl,
        'secret_token' => $secret,
        'drop_pending_updates' => 'false',
    ];

    $apiUrl = 'https://api.telegram.org/bot' . BOT_TOKEN
        . '/setWebhook?' . http_build_query($params);

    $response = @file_get_contents($apiUrl);

    if ($response === false) {
        $lastError = error_get_last();
        $message = is_array($lastError) && isset($lastError['message'])
            ? $lastError['message']
            : 'unknown error';

        throw new RuntimeException('Telegram API request failed: ' . $message);
    }

    $decoded = json_decode($response, true);

    if (!is_array($decoded)) {
        throw new RuntimeException('Telegram returned invalid JSON.');
    }

    echo json_encode([
        'status' => !empty($decoded['ok']) ? 'success' : 'error',
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
