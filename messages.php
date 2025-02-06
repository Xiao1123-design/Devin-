<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch conversations
$conversations_sql = "SELECT DISTINCT 
    CASE 
        WHEN m.sender_id = ? THEN m.receiver_id 
        ELSE m.sender_id 
    END as other_user_id,
    u.username,
    (SELECT message_text 
     FROM messages 
     WHERE (sender_id = ? AND receiver_id = other_user_id) 
        OR (sender_id = other_user_id AND receiver_id = ?)
     ORDER BY created_at DESC 
     LIMIT 1) as last_message,
    (SELECT created_at 
     FROM messages 
     WHERE (sender_id = ? AND receiver_id = other_user_id) 
        OR (sender_id = other_user_id AND receiver_id = ?)
     ORDER BY created_at DESC 
     LIMIT 1) as last_message_time
FROM messages m
JOIN users u ON u.user_id = CASE 
    WHEN m.sender_id = ? THEN m.receiver_id 
    ELSE m.sender_id 
END
WHERE m.sender_id = ? OR m.receiver_id = ?
ORDER BY last_message_time DESC";

$stmt = $conn->prepare($conversations_sql);
$stmt->bind_param("iiiiiiii", 
    $_SESSION['user_id'], 
    $_SESSION['user_id'], 
    $_SESSION['user_id'],
    $_SESSION['user_id'],
    $_SESSION['user_id'],
    $_SESSION['user_id'],
    $_SESSION['user_id'],
    $_SESSION['user_id']
);
$stmt->execute();
$conversations = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - ResellU</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/messages.css">
</head>
<body class="dashboard-body">
    <?php include 'includes/header.php'; ?>
    
    <main class="messages-container">
        <div class="conversations-list">
            <?php while($conv = $conversations->fetch_assoc()): ?>
                <a href="chat.php?user=<?php echo $conv['other_user_id']; ?>" 
                   class="conversation-item">
                    <div class="conversation-info">
                        <h3><?php echo htmlspecialchars($conv['username']); ?></h3>
                        <p class="last-message">
                            <?php echo htmlspecialchars(substr($conv['last_message'], 0, 50)) . '...'; ?>
                        </p>
                    </div>
                    <span class="message-time">
                        <?php echo date('M j, g:i a', strtotime($conv['last_message_time'])); ?>
                    </span>
                </a>
            <?php endwhile; ?>
        </div>
        
        <div class="chat-placeholder">
            <p>Select a conversation to start chatting</p>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
