<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reviewer_id = $_SESSION['user_id'];
    $reviewed_id = intval($_POST['reviewed_id']);
    $rating = intval($_POST['rating']);
    $comment = isset($_POST['comment']) ? $conn->real_escape_string($_POST['comment']) : null;
    
    // Validate rating
    if ($rating < 1 || $rating > 5) {
        header("Location: product.php?id=" . $_GET['id'] . "&error=invalid_rating");
        exit();
    }
    
    // Check if user is reviewing themselves
    if ($reviewer_id === $reviewed_id) {
        header("Location: product.php?id=" . $_GET['id'] . "&error=self_review");
        exit();
    }
    
    // Check if user has already reviewed this seller
    $check_sql = "SELECT review_id FROM reviews 
                  WHERE reviewer_id = ? AND reviewed_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $reviewer_id, $reviewed_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        header("Location: product.php?id=" . $_GET['id'] . "&error=already_reviewed");
        exit();
    }
    
    // Insert review
    $sql = "INSERT INTO reviews (reviewer_id, reviewed_id, rating, comment) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $reviewer_id, $reviewed_id, $rating, $comment);
    
    if ($stmt->execute()) {
        header("Location: product.php?id=" . $_GET['id'] . "&success=review_posted");
    } else {
        header("Location: product.php?id=" . $_GET['id'] . "&error=review_failed");
    }
    
    $stmt->close();
}
$conn->close();
?>
