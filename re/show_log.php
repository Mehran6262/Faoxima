<?php
// re/show_log.php
header('Content-Type: text/plain; charset=utf-8');

// برای امنیت بیشتر اگر مایل بودید
$secret = '123456'; 
if (isset($_GET['pwd']) && $_GET['pwd'] !== $secret) {
    die("Access Denied");
}

$baseDir = dirname(__DIR__);
$logFiles = [
    'runtime.log'    => $baseDir . '/logs/runtime.log',
    'php-error.log'  => $baseDir . '/logs/php-error.log',
    'eval_trace.log' => __DIR__ . '/rx/index/eval_trace.log',
];

echo "=================== LOG VIEWER ===================\n\n";

foreach ($logFiles as $name => $path) {
    echo "--- [ File: $name ] ---\n";
    if (file_exists($path)) {
        $lines = file($path);
        // نمایش ۵۰ خط آخر فایل
        $lastLines = array_slice($lines, -50);
        echo implode('', $lastLines);
    } else {
        echo "File does not exist yet at: $path\n";
    }
    echo "\n\n";
}
