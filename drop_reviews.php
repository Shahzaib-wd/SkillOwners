<?php
require_once 'config.php';
try {
    $conn = getDBConnection();
    $conn->exec("DROP TABLE IF EXISTS reviews");
    echo "Reviews table dropped successfully.\n";
} catch (PDOException $e) {
    echo "Error dropping table: " . $e->getMessage() . "\n";
}
