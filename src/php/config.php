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
    // Try multiple possible locations for the SQL file
    $possible_paths = [
        dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sql',
        dirname(__DIR__) . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'database.sql',
        __DIR__ . DIRECTORY_SEPARATOR . 'database.sql',
        __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'database.sql'
    ];

    $sql_file = null;
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            $sql_file = $path;
            break;
        }
    }

    if (!$sql_file) {
        throw new Exception("找不到数据库初始化文件。请确保database.sql文件存在于以下位置之一：\n" .
                          implode("\n", array_map(function($path) { 
                              return "- " . str_replace('\\', '/', $path); 
                          }, $possible_paths)));
    }

    if (!is_readable($sql_file)) {
        throw new Exception("无法读取SQL文件，请检查文件权限: " . $sql_file);
    }

    $tables_sql = file_get_contents($sql_file);
    if ($tables_sql === false) {
        throw new Exception("读取SQL文件失败: " . $sql_file);
    }

    if (empty(trim($tables_sql))) {
        throw new Exception("SQL文件内容为空: " . $sql_file);
    }

    // Execute SQL queries
    if (!$conn->multi_query($tables_sql)) {
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
