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
    $sql_dir = realpath(__DIR__ . '/../sql');
    if (!$sql_dir || !is_dir($sql_dir)) {
        throw new Exception("SQL目录不存在: " . __DIR__ . '/../sql');
    }
    
    $sql_file = $sql_dir . DIRECTORY_SEPARATOR . 'database.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("SQL文件不存在: " . $sql_file);
    }
    
    $tables_sql = file_get_contents($sql_file);
    if ($tables_sql === false) {
        throw new Exception("无法读取SQL文件: " . $sql_file);
    }
    
    if (!$conn->multi_query($tables_sql)) {
        throw new Exception("初始化数据表失败: " . $conn->error);
    }
    
    // Clear results from multi_query and check for errors
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
        if ($conn->error) {
            throw new Exception("Error in table creation: " . $conn->error);
        }
    } while ($conn->more_results() && $conn->next_result());

    // Verify tables exist
    $check_tables_sql = "SHOW TABLES LIKE 'anonymous_ratings'";
    $result = $conn->query($check_tables_sql);
    if (!$result || $result->num_rows === 0) {
        throw new Exception("Table creation failed: anonymous_ratings table not found");
    }
} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    die("Database Error: " . $e->getMessage());
}
?>
