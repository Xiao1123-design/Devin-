<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '请先登录']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$message_id = intval($data['message_id']);

// Verify message belongs to current user
$check_sql = "SELECT receiver_id FROM messages WHERE message_id = ?";
$stmt = $conn->prepare($check_sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if (!$stmt->bind_param("i", $message_id)) {
    die("Binding parameters failed: " . $stmt->error);
}

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
if ($result === false) {
    die("Getting result failed: " . $stmt->error);
}

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => '消息不存在']);
    exit();
}

$message = $result->fetch_assoc();
if ($message['receiver_id'] !== $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => '无权删除此消息']);
    exit();
}

// Delete the message
$delete_sql = "DELETE FROM messages WHERE message_id = ?";
$stmt = $conn->prepare($delete_sql);
$stmt->bind_param("i", $message_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => '消息已删除']);
} else {
    echo json_encode(['success' => false, 'message' => '删除失败，请重试']);
}
