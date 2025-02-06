<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $nationality = $conn->real_escape_string($_POST['nationality']);
    $age = empty($_POST['age']) ? null : intval($_POST['age']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $address = $conn->real_escape_string($_POST['address']);
    
    // Check if username or email already exists for other users
    $check_sql = "SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ssi", $username, $email, $_SESSION['user_id']);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        header("Location: profile.php?error=duplicate_credentials");
        exit();
    }
    
    $sql = "UPDATE users SET username = ?, email = ?, nationality = ?, age = ?, gender = ?, address = ? 
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissi", $username, $email, $nationality, $age, $gender, $address, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $_SESSION['username'] = $username;
        header("Location: profile.php?success=profile_updated");
    } else {
        header("Location: profile.php?error=update_failed");
    }
    
    $stmt->close();
}
$conn->close();
?>
