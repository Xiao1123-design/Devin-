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
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : null;
    
    // Validate rating
    if (!is_numeric($_POST['rating']) || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => '评分必须是1到5之间的数字']);
        exit();
    }
    
    // Validate comment length
    if ($comment !== null && mb_strlen($comment) > 1000) {
        echo json_encode(['success' => false, 'message' => '评论不能超过1000个字符']);
        exit();
    }
    
    $comment = $comment !== null ? $conn->real_escape_string($comment) : null;
    
    // Check for rate limiting
    $rate_limit_sql = "SELECT COUNT(*) as recent_ratings FROM anonymous_ratings 
                       WHERE rater_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
    $stmt = $conn->prepare($rate_limit_sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result['recent_ratings'] >= 10) {
        echo json_encode(['success' => false, 'message' => '您的评分次数过多，请稍后再试']);
        exit();
    }
    
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
    
    // Start transaction
    $conn->begin_transaction();
    try {
        // Insert rating
        $sql = "INSERT INTO anonymous_ratings (rater_id, rated_id, rating, comment) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $_SESSION['user_id'], $rated_id, $rating, $comment);
        
        if (!$stmt->execute()) {
            throw new Exception("评分插入失败");
        }

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
        
        if (!$update_stmt->execute()) {
            throw new Exception("更新用户评分失败");
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => '评价成功']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
