<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    header("Location: messages.php");
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = intval($_POST['receiver_id']);
$message = trim($_POST['message']);

if (!empty($message)) {
    $sql = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    $stmt->execute();
}

header("Location: chat.php?user=" . $receiver_id);
exit();
?>
