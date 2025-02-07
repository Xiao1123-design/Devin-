<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$username = isset($data['username']) ? trim($data['username']) : '';

if (empty($username)) {
    echo json_encode(['available' => false, 'message' => '用户名不能为空']);
    exit();
}

$sql = "SELECT user_id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => '验证失败，请重试']);
    exit();
}

$stmt->bind_param("s", $username);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => '验证失败，请重试']);
    exit();
}

$result = $stmt->get_result();
if ($result === false) {
    echo json_encode(['success' => false, 'message' => '验证失败，请重试']);
    exit();
}

$isAvailable = $result->num_rows === 0;
echo json_encode([
    'success' => true,
    'available' => $isAvailable,
    'message' => $isAvailable ? '用户名可用' : '用户名已被使用'
]);
