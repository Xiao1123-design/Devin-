<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '请先登录']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rated_id = intval($_POST['rated_id']);
    $rating = intval($_POST['rating']);
    $comment = isset($_POST['comment']) ? $conn->real_escape_string($_POST['comment']) : null;
    
    // Prevent self-rating
    if ($rated_id === $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => '不能给自己评分']);
        exit();
    }
    
    // Check if already rated
    $check_sql = "SELECT rating_id FROM anonymous_ratings 
                  WHERE rater_id = ? AND rated_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $_SESSION['user_id'], $rated_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => '您已经评价过此用户']);
        exit();
    }
    
    // Insert rating
    $sql = "INSERT INTO anonymous_ratings (rater_id, rated_id, rating, comment) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $_SESSION['user_id'], $rated_id, $rating, $comment);
    
    if ($stmt->execute()) {
        // Update user's average rating
        $update_sql = "UPDATE users u 
                      SET rating = (
                          SELECT AVG(rating) 
                          FROM anonymous_ratings 
                          WHERE rated_id = u.user_id
                      ) 
                      WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $rated_id);
        $update_stmt->execute();
        
        echo json_encode(['success' => true, 'message' => '评价成功']);
    } else {
        echo json_encode(['success' => false, 'message' => '评价失败']);
    }
}
?>
