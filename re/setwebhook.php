<?php
header('Content-Type: text/plain; charset=utf-8');

$botToken = getenv('BOT_TOKEN'); // یا هر اسمی که تو Render گذاشتی
$secret   = getenv('TELEGRAM_WEBHOOK_SECRET');

$webhookUrl = 'https://faoxima-1.onrender.com/re/index.php'; // آدرس دقیق ورودی وب‌هوک

if (!$botToken) { die("APIKEY env missing\n"); }
if (!$secret)   { die("TELEGRAM_WEBHOOK_SECRET env missing\n"); }

$api = "https://api.telegram.org/bot{$botToken}/setWebhook";
$params = [
  'url' => $webhookUrl,
  'secret_token' => $secret,
];

$ch = curl_init($api);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => $params,
]);
$res = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
  die("cURL error: $err\n");
}
echo $res . "\n";
