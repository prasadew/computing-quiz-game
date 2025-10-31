<?php
require_once 'config/database.php';

try {
    global $database;
    $conn = $database->getConnection();
    echo "✅ Database connected successfully!";
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>