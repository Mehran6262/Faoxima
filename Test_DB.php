<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';

echo "<h2>تست اتصال دیتابیس Faoxima</h2>";
echo "Host: " . htmlspecialchars($GLOBALS['dbname'] ?? '') . "<br>";
echo "User: " . htmlspecialchars($GLOBALS['usernamedb'] ?? '') . "<br>";
echo "Password length: " . strlen($GLOBALS['passworddb'] ?? '') . " chars<br><hr>";

try {
    if (isset($pdo) && $pdo instanceof PDO) {
        echo "<h3 style='color:green;'>✅ اتصال PDO با موفقیت برقرار شد!</h3>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "تعداد جدول‌های موجود: " . count($tables) . "<br>";
        echo "<pre>" . print_r($tables, true) . "</pre>";
    } else {
        echo "<h3 style='color:red;'>❌ شیء PDO ساخته نشد!</h3>";
    }
} catch (Exception $e) {
    echo "<h3 style='color:red;'>❌ خطا در PDO:</h3>" . $e->getMessage();
}

echo "<hr>";

try {
    if (isset($connect) && $connect instanceof mysqli) {
        echo "<h3 style='color:green;'>✅ اتصال MySQLi برقرار است!</h3>";
    } else {
        echo "<h3 style='color:red;'>❌ شیء MySQLi متصل نشد!</h3>";
    }
} catch (Exception $e) {
    echo "<h3 style='color:red;'>❌ خطا در MySQLi:</h3>" . $e->getMessage();
}
