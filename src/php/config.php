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
    // Get absolute paths
    $script_dir = str_replace('\\', '/', __DIR__);
    $root_dir = str_replace('\\', '/', dirname($script_dir));
    
    // For PHPStudy Pro, get the WWW root directory
    if (strpos($script_dir, 'phpstudy_pro') !== false) {
        // Extract the WWW/RESELLU part from the path
        if (preg_match('/(.*?WWW\/RESELLU)/', $script_dir, $matches)) {
            $root_dir = str_replace('\\', '/', $matches[1]);
        }
    }
    
    // Define all possible SQL file locations
    $possible_locations = [
        $root_dir . '/database/database.sql',
        $root_dir . '/sql/database.sql',
        $script_dir . '/database.sql',
        dirname($script_dir) . '/sql/database.sql',
        $root_dir . '/database.sql'
    ];
    
    // Try to find existing SQL file
    $sql_file = null;
    foreach ($possible_locations as $location) {
        if (file_exists($location)) {
            $sql_file = $location;
            break;
        }
    }
    
    // If not found, try to create database directory and copy file
    if (!$sql_file) {
        $database_dir = $root_dir . '/database';
        if (!is_dir($database_dir) && !mkdir($database_dir, 0755, true)) {
            throw new Exception("无法创建数据库目录: " . $database_dir);
        }
        
        $target_file = $database_dir . '/database.sql';
        foreach ($possible_locations as $source) {
            if (file_exists($source) && copy($source, $target_file)) {
                $sql_file = $target_file;
                break;
            }
        }
    }
    
    if (!$sql_file) {
        $error_msg = "找不到数据库初始化文件。\n";
        $error_msg .= "请确保database.sql文件存在于以下位置之一：\n";
        foreach ($possible_locations as $path) {
            $error_msg .= "- " . str_replace('\\', '/', $path) . "\n";
        }
        $error_msg .= "当前PHP目录: " . str_replace('\\', '/', $script_dir);
        throw new Exception($error_msg);
    }

    $sql_file = null;
    foreach ($possible_paths as $path) {
        $normalized_path = str_replace(['\\', '//'], '/', $path);
        if (file_exists($normalized_path)) {
            $sql_file = $normalized_path;
            break;
        }
    }

    if (!$sql_file) {
        throw new Exception("找不到数据库初始化文件。\n请确保database.sql文件存在于以下位置之一：\n" .
                          implode("\n", array_map(function($path) {
                              return "- " . str_replace(['\\', '//'], '/', $path);
                          }, $possible_paths)) . "\n" .
                          "当前PHP目录: " . str_replace('\\', '/', __DIR__));
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
