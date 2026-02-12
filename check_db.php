<?php
require_once 'config.php';

try {
    $conn = getDBConnection();
    $stmt = $conn->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }

    // Check if conversations table exists
    if (in_array('conversations', $tables)) {
        echo "\nConversations table exists!\n";

        // Check structure
        $stmt = $conn->query('DESCRIBE conversations');
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Conversations table structure:\n";
        foreach ($columns as $col) {
            echo "- {$col['Field']}: {$col['Type']}\n";
        }
    } else {
        echo "\nConversations table does NOT exist!\n";
    }

    // Check messages table
    if (in_array('messages', $tables)) {
        echo "\nMessages table exists!\n";

        $stmt = $conn->query('DESCRIBE messages');
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Messages table structure:\n";
        foreach ($columns as $col) {
            echo "- {$col['Field']}: {$col['Type']}\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
