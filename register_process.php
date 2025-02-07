<?php
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_type = $conn->real_escape_string($_POST['user_type']);
    
    // Check if username or email already exists
    $check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        echo json_encode(['success' => false, 'message' => '注册失败：' . $conn->error]);
        exit();
    }

    if (!$check_stmt->bind_param("ss", $username, $email)) {
        echo json_encode(['success' => false, 'message' => '注册失败：' . $check_stmt->error]);
        exit();
    }

    if (!$check_stmt->execute()) {
        echo json_encode(['success' => false, 'message' => '注册失败：' . $check_stmt->error]);
        exit();
    }

    $result = $check_stmt->get_result();
    if ($result === false) {
        echo json_encode(['success' => false, 'message' => '注册失败：' . $check_stmt->error]);
        exit();
    }
    
    if ($result->num_rows > 0) {
        $existing_user = $result->fetch_assoc();
        if ($existing_user['username'] === $username) {
            echo json_encode(['success' => false, 'message' => '用户名已被使用']);
        } else {
            echo json_encode(['success' => false, 'message' => '邮箱已被注册']);
        }
        exit();
    }
    
    // Insert new user
    $sql = "INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => '注册失败：' . $conn->error]);
        exit();
    }

    if (!$stmt->bind_param("ssss", $username, $email, $password, $user_type)) {
        echo json_encode(['success' => false, 'message' => '注册失败：' . $stmt->error]);
        exit();
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => '注册成功！请登录']);
    } else {
        echo json_encode(['success' => false, 'message' => '注册失败，请重试']);
    }
    
    $stmt->close();
}
$conn->close();
?>
