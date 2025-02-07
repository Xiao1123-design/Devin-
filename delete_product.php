<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '请先登录']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = intval($data['product_id']);

// Verify product belongs to current user
$check_sql = "SELECT seller_id, image_path FROM products WHERE product_id = ?";
$stmt = $conn->prepare($check_sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if (!$stmt->bind_param("i", $product_id)) {
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
    echo json_encode(['success' => false, 'message' => '商品不存在']);
    exit();
}

$product = $result->fetch_assoc();
if ($product['seller_id'] !== $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => '无权删除此商品']);
    exit();
}

// Delete the product image if it exists
if ($product['image_path'] && file_exists($product['image_path'])) {
    unlink($product['image_path']);
}

// Delete the product
$delete_sql = "DELETE FROM products WHERE product_id = ?";
$stmt = $conn->prepare($delete_sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if (!$stmt->bind_param("i", $product_id)) {
    die("Binding parameters failed: " . $stmt->error);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => '商品已删除']);
} else {
    echo json_encode(['success' => false, 'message' => '删除失败，请重试']);
}
