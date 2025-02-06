<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '请先登录']);
    exit();
}

$user_id = intval($_GET['user_id']);

// Prevent self-profile viewing (although not strictly necessary)
if ($user_id === $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => '无法查看自己的资料']);
    exit();
}

$sql = "SELECT user_id, username, email, age, nationality, address, user_type, avatar_path 
        FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'user' => $user
    ]);
} else {
    echo json_encode(['success' => false, 'message' => '用户不存在']);
}
