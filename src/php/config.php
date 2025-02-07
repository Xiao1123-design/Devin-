<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '2567662');
define('DB_NAME', 'resellu');

// Define image paths
define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('UPLOAD_PATH', SITE_ROOT . '/public/images/');
define('UPLOAD_URL', '/public/images/');

// Create database if not exists
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    if (!$conn->query($sql)) {
        throw new Exception("Error creating database: " . $conn->error);
    }

    // Close initial connection and connect to the database
    $conn->close();
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Initialize database tables
    $tables_sql = file_get_contents(__DIR__ . '/../sql/database.sql');
    if (!$conn->multi_query($tables_sql)) {
        throw new Exception("Error initializing tables: " . $conn->error);
    }
    
    // Clear results from multi_query
    while ($conn->more_results()) {
        $conn->next_result();
    }
} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    die("Database Error: " . $e->getMessage());
}
?>
