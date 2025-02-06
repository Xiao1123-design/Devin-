<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    echo json_encode(['success' => false, 'message' => '无效请求']);
    exit();
}

// Prevent self-messaging
if ($_SESSION['user_id'] == $_POST['receiver_id']) {
    echo json_encode(['success' => false, 'message' => '不能与自己聊天']);
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = intval($_POST['receiver_id']);
$message = trim($_POST['message']);

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => '消息不能为空']);
    exit();
}

$sql = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => '消息已发送']);
} else {
    echo json_encode(['success' => false, 'message' => '发送失败，请重试']);
}
exit();
?>
