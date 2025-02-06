<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username']);

if (empty($username)) {
    echo json_encode(['available' => false, 'message' => '用户名不能为空']);
    exit();
}

$sql = "SELECT user_id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

echo json_encode([
    'available' => $stmt->get_result()->num_rows === 0,
    'message' => $stmt->get_result()->num_rows === 0 ? '用户名可用' : '用户名已被使用'
]);
