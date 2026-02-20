<?php
require_once 'config.php';
$db = getDBConnection();
$res = $db->query("DESCRIBE blog_posts")->fetchAll();
foreach($res as $row) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
