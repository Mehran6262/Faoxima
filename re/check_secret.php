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
        throw new RuntimeException('Could not read webhook_secret_token.');
    }

    $row = $result->fetch(PDO::FETCH_ASSOC);
    $secret = '';

    if (is_array($row) && isset($row['webhook_secret_token'])) {
        $secret = (string) $row['webhook_secret_token'];
    }

    if ($secret === '') {
        throw new RuntimeException('webhook_secret_token is missing in DB.');
    }

    /*
     * ساختار احتمالی جدول setting:
     * - ردیف‌های key/value
     * - یا ستون‌هایی مانند bot_token / token
     *
     * ابتدا ستون‌های جدول را می‌خوانیم و فقط نام ستون‌های مشکوک را نگه می‌داریم.
     */
    $columnsQuery = $connection->query(
        "SELECT column_name
         FROM information_schema.columns
         WHERE table_schema = 'public' AND table_name = 'setting'
         ORDER BY ordinal_position"
    );

    if ($columnsQuery === false) {
        throw new RuntimeException('Could not inspect setting table.');
    }

    $columns = $columnsQuery->fetchAll(PDO::FETCH_COLUMN);
    $tokenColumn = null;

    foreach ($columns as $column) {
        $normalized = strtolower((string) $column);

        if (
            $normalized === 'bot_token' ||
            $normalized === 'bottoken' ||
            $normalized === 'telegram_token' ||
            $normalized === 'telegram_bot_token' ||
            $normalized === 'token'
        ) {
            $tokenColumn = $column;
            break;
        }
    }

    $botToken = '';

    if ($tokenColumn !== null) {
        // نام ستون از DB خوانده شده و quote شده است.
        $statement = $connection            'SELECT "' . str_replace('"', '('"', '""', $tokenColumn) .
            '" FROM setting LIMIT 1'
        );

        if ($statement !== false) {
            $tokenRow = $statement->fetch(PDO::FETCH_ASSOC);

            if (is_array($tokenRow) && isset($tokenRow[$tokenColumn])) {
                $botToken =tokenColumn];
            }
        }
   Column];
            }
        }
    }

    if ($botToken === '') {
        throw new RuntimeException(
            'Bot token column not found or empty. Setting columns: ' .
            implode(', ', $columns)
        );
    }

    $webhookUrl = 'https://faoxima-1.onrender.com/index.php';

    $apiUrl = 'https://api.telegram.org/bot' . $botToken . '/setWebhook?' .
        http_build_query([
            'url' => $webhookUrl,
            'secret_token' => $secret,
            'drop_pending_updates' => 'false',
        ]);

    $response = @file_get_contents($apiUrl);

    if ($response === false) {
        $error = error_get_last();
        throw new RuntimeException(
            'Telegram API request failed: ' .
            (is_array($error) && isset($error['message'])
                ? $error['message']
                : 'unknown error')
        );
    }

    $telegram = json_decode($response, true);

    if (!is_array($telegram)) {
        throw new RuntimeException('Telegram returned invalid JSON.');
    }

    echo json_encode([
        'status' => !empty($telegram['ok']) ? 'success' : 'error',
        'telegram_ok' => $telegram['ok'] ?? null,
        'telegram_description' => $telegram['description'] ?? null,
        'webhook_url_set' => $webhookUrl,
        'token_source_column' => $tokenColumn,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
