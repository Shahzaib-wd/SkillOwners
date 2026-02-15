<?php
require_once 'config.php';
$db = getDBConnection();

$stmt = $db->query("SELECT NOW(), UTC_TIMESTAMP()");
$times = $stmt->fetch(PDO::FETCH_NUM);
echo "DB NOW(): " . $times[0] . "\n";
echo "DB UTC(): " . $times[1] . "\n";
echo "PHP date('Y-m-d H:i:s'): " . date('Y-m-d H:i:s') . "\n";
echo "PHP date_default_timezone_get(): " . date_default_timezone_get() . "\n";
