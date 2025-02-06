<?php
require_once 'config.php';

// Check if admin user exists
$check_sql = "SELECT user_id FROM users WHERE username = 'admin'";
$result = $conn->query($check_sql);

if ($result->num_rows === 0) {
    // Create admin user
    $username = 'admin';
    $password = password_hash('admin', PASSWORD_DEFAULT);
    $email = 'admin@resellu.com';
    $user_type = 'admin';
    
    $sql = "INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $user_type);
    $stmt->execute();
}

$conn->close();
?>
