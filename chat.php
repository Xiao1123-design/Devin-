<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['user'])) {
    header("Location: messages.php");
    exit();
}

$other_user_id = intval($_GET['user']);

// Fetch other user's details
$user_sql = "SELECT username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $other_user_id);
$stmt->execute();
$other_user = $stmt->get_result()->fetch_assoc();

// Fetch messages
$messages_sql = "SELECT m.*, u.username 
                FROM messages m 
                JOIN users u ON m.sender_id = u.user_id 
                WHERE (sender_id = ? AND receiver_id = ?) 
                   OR (sender_id = ? AND receiver_id = ?) 
                ORDER BY created_at ASC";
$stmt = $conn->prepare($messages_sql);
$stmt->bind_param("iiii", 
    $_SESSION['user_id'], 
    $other_user_id,
    $other_user_id,
    $_SESSION['user_id']
);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($other_user['username']); ?> - ResellU</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/messages.css">
    <link rel="stylesheet" href="css/chat.css">
</head>
<body class="dashboard-body">
    <?php include 'includes/header.php'; ?>
    
    <main class="chat-container">
        <div class="chat-header">
            <h2>Chat with <?php echo htmlspecialchars($other_user['username']); ?></h2>
        </div>
        
        <div class="messages-list" id="messages-list">
            <?php while($message = $messages->fetch_assoc()): ?>
                <div class="message <?php echo $message['sender_id'] == $_SESSION['user_id'] ? 'sent' : 'received'; ?>">
                    <div class="message-content">
                        <?php echo htmlspecialchars($message['message_text']); ?>
                    </div>
                    <div class="message-meta">
                        <?php echo date('g:i a', strtotime($message['created_at'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
        <form class="message-form" action="send_message.php" method="POST">
            <input type="hidden" name="receiver_id" value="<?php echo $other_user_id; ?>">
            <textarea name="message" placeholder="Type your message..." required></textarea>
            <button type="submit" class="btn-send">Send</button>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Scroll to bottom of messages
        const messagesList = document.getElementById('messages-list');
        messagesList.scrollTop = messagesList.scrollHeight;
        
        // Auto-resize textarea
        const textarea = document.querySelector('textarea');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    </script>
</body>
</html>
