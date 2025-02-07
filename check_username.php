<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? '');

if (empty($username)) {
    echo json_encode(['available' => false, 'message' => '用户名不能为空']);
    exit();
}

try {
    $sql = "SELECT user_id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $isAvailable = $result->num_rows === 0;
    
    echo json_encode([
        'success' => true,
        'available' => $isAvailable,
        'message' => $isAvailable ? '用户名可用' : '用户名已被使用'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'available' => false,
        'message' => '验证用户名时出错'
    ]);
}
