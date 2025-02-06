<?php
session_start();
require_once '../config.php';

// Check if user is admin
$admin_sql = "SELECT user_type FROM users WHERE user_id = ?";
$stmt = $conn->prepare($admin_sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!isset($_SESSION['user_id']) || $user['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle DELETE action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    
    // Don't allow deleting self
    if ($user_id === $_SESSION['user_id']) {
        header("Location: index.php?error=cannot_delete_self");
        exit();
    }
    
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        header("Location: index.php?success=user_deleted");
    } else {
        header("Location: index.php?error=delete_failed");
    }
    exit();
}

// Handle CREATE/UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $user_type = $conn->real_escape_string($_POST['user_type']);
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
    
    // Check if username or email already exists
    $check_sql = "SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_id = $user_id ?? 0;
    $check_stmt->bind_param("ssi", $username, $email, $check_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        header("Location: index.php?error=duplicate_credentials");
        exit();
    }
    
    if ($user_id) {
        // UPDATE
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = ?, email = ?, password = ?, user_type = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $username, $email, $password, $user_type, $user_id);
        } else {
            $sql = "UPDATE users SET username = ?, email = ?, user_type = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $username, $email, $user_type, $user_id);
        }
        
        if ($stmt->execute()) {
            header("Location: index.php?success=user_updated");
        } else {
            header("Location: index.php?error=update_failed");
        }
    } else {
        // CREATE
        if (empty($_POST['password'])) {
            header("Location: index.php?error=password_required");
            exit();
        }
        
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $password, $user_type);
        
        if ($stmt->execute()) {
            header("Location: index.php?success=user_added");
        } else {
            header("Location: index.php?error=create_failed");
        }
    }
}
?>
