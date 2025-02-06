<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $expected_price = floatval($_POST['expected_price']);
    
    $sql = "INSERT INTO requests (user_id, title, description, expected_price) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issd", $_SESSION['user_id'], $title, $description, $expected_price);
    
    if ($stmt->execute()) {
        header("Location: requests.php?success=request_posted");
    } else {
        header("Location: requests.php?error=post_failed");
    }
    
    $stmt->close();
}
$conn->close();
?>
