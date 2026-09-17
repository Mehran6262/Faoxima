<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php'; // اگر دیتابیس در فایل دیگری مثل database.php متصل می‌شود نام همان را بگذار

try {
    // اگر متغیر $pdo در پروژه تعریف شده از همان استفاده کن، در غیر این صورت اتصال مستقیم:
    if (!isset($pdo)) {
        $pdo = new PDO("pgsql:host=" . DB_HOST . ";port=" . (defined('DB_PORT') ? DB_PORT : 5432) . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    }
    $row = $pdo->query("SELECT webhook_secret_token FROM setting LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    echo json_encode([
        'status' => 'success',
        'has_secret' => !empty($row['webhook_secret_token']),
        'secret_length' => strlen($row['webhook_secret_token'] ?? ''),
        'secret_sample' => !empty($row['webhook_secret_token']) ? substr($row['webhook_secret_token'], 0, 3) . '***' : null
    ]);
} catch (\Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
