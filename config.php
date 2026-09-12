<?php

$dbhost     = getenv('DB_HOST') ?: 'be28agyjhxybhkcdkfss-mysql.services.clever-cloud.com';
$dbname     = getenv('DB_NAME') ?: 'be28agyjhxybhkcdkfss';
$usernamedb = getenv('DB_USER') ?: 'ujogaarkshw5jg5m';
$passworddb = getenv('DB_PASSWORD') ?: '';
$dbport     = getenv('DB_PORT') ?: 3306;

$connect = null;
$pdo     = null;
$dsn     = 'mysql:host=' . $dbhost . ';port=' . $dbport . ';dbname=' . $dbname . ';charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
];

if ($dbname !== '' && $usernamedb !== '') {
    if (function_exists('mysqli_report')) {
        @mysqli_report(MYSQLI_REPORT_OFF);
    }
    try {
        $connect = @mysqli_connect($dbhost, $usernamedb, $passworddb, $dbname, (int)$dbport);
    } catch (\Throwable $rxMysqliConnectError) {
        $connect = null;
        error_log('config.php mysqli_connect failed: ' . $rxMysqliConnectError->getMessage());
    }
    if ($connect instanceof mysqli) {
        @mysqli_set_charset($connect, 'utf8mb4');
    } else {
        $connect = null;
    }

    try {
        $pdo = new PDO($dsn, $usernamedb, $passworddb, $options);
    } catch (\PDOException $rxPdoError) {
        $pdo = null;
        error_log('config.php PDO connection failed: ' . $rxPdoError->getMessage());
    }
}

$APIKEY      = getenv('APIKEY') ?: '8883033477:AAFiyB_FI5EnJrkLBR0NJbTMczLSrPmpmQ0';
$adminnumber = getenv('ADMIN_NUMBER') ?: '133495331';
$domainhosts = getenv('DOMAIN_HOSTS') ?: 'faoxima-1.onrender.com';
$usernamebot = getenv('USERNAME_BOT') ?: 'Robatman1362bot';

$telegramCurlTimeout        = 10;
$telegramStrictIpValidation = false;
$domainhosts                = rtrim(preg_replace('#^https?://#', '', $domainhosts), '/');

if (!defined('APP_ORIGIN') && $domainhosts !== '') {
    define('APP_ORIGIN', 'https://' . $domainhosts);
}

$GLOBALS['dbname']                     = $dbname;
$GLOBALS['usernamedb']                 = $usernamedb;
$GLOBALS['passworddb']                 = $passworddb;
$GLOBALS['dsn']                        = $dsn;
$GLOBALS['options']                    = $options;
$GLOBALS['pdo']                        = $pdo;
$GLOBALS['connect']                    = $connect;
$GLOBALS['APIKEY']                     = $APIKEY;
$GLOBALS['adminnumber']                = $adminnumber;
$GLOBALS['domainhosts']                = $domainhosts;
$GLOBALS['usernamebot']                = $usernamebot;
$GLOBALS['telegramCurlTimeout']        = $telegramCurlTimeout;
$GLOBALS['telegramStrictIpValidation'] = $telegramStrictIpValidation;

if (file_exists(__DIR__ . '/proxy.php')) {
    require_once __DIR__ . '/proxy.php';
}
