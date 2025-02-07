<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['user'])) {
    header("Location: messages.php");
    exit();
}

$other_user_id = intval($_GET['user']);

// Prevent self-messaging
if ($other_user_id === $_SESSION['user_id']) {
    echo "<script>
        alert('不能与自己聊天');
        window.location.href = 'messages.php';
    </script>";
    exit();
}

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
    <link rel="stylesheet" href="css/alerts.css">
    <script src="js/alerts.js"></script>
</head>
<body class="dashboard-body">
    <?php include 'includes/header.php'; ?>
    
    <?php include 'includes/back_button.php'; ?>
    <main class="chat-container">
        <div class="chat-header">
            <img src="<?php echo isset($other_user['avatar_path']) ? $other_user['avatar_path'] : UPLOAD_URL . 'default-avatar.png'; ?>" 
                 alt="<?php echo htmlspecialchars($other_user['username']); ?>" 
                 class="chat-avatar"
                 onclick="showUserProfile(<?php echo $other_user_id; ?>)"
                 title="点击查看用户资料">
            <h2>Chat with <?php echo htmlspecialchars($other_user['username']); ?></h2>
        </div>
        
        <!-- User Profile Modal -->
        <div id="userProfileModal" class="modal">
            <div class="profile-content">
                <span class="close" onclick="closeUserProfile()">&times;</span>
                <div class="profile-header">
                    <img id="profileAvatar" src="" alt="User Avatar">
                    <h2 id="profileUsername"></h2>
                    <span id="profileUserType" class="user-type-badge"></span>
                </div>
                <div class="profile-details">
                    <div class="detail-group">
                        <label>邮箱</label>
                        <p id="profileEmail"></p>
                    </div>
                    <div class="detail-group">
                        <label>年龄</label>
                        <p id="profileAge"></p>
                    </div>
                    <div class="detail-group">
                        <label>国籍</label>
                        <p id="profileNationality"></p>
                    </div>
                    <div class="detail-group">
                        <label>地址</label>
                        <p id="profileAddress"></p>
                    </div>
                </div>
            </div>
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
        
        <form class="message-form" id="messageForm" onsubmit="handleSendMessage(event)">
            <input type="hidden" name="receiver_id" value="<?php echo $other_user_id; ?>">
            <textarea name="message" placeholder="Type your message..." required></textarea>
            <button type="submit" class="btn-send">Send</button>
        </form>
        
        <script>
        async function handleSendMessage(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch('send_message.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                if (data.success) {
                    showAlert(data.message, 'success');
                    form.reset();
                    // Refresh messages
                    location.reload();
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'error');
            }
        }
        </script>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Profile view functionality
        function showUserProfile(userId) {
            fetch(`get_user_profile.php?user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('profileAvatar').src = data.user.avatar_path || '/public/images/default-avatar.png';
                        document.getElementById('profileUsername').textContent = data.user.username;
                        document.getElementById('profileEmail').textContent = data.user.email;
                        document.getElementById('profileAge').textContent = data.user.age;
                        document.getElementById('profileNationality').textContent = data.user.nationality;
                        document.getElementById('profileAddress').textContent = data.user.address;
                        document.getElementById('profileUserType').textContent = data.user.user_type;
                        document.getElementById('userProfileModal').style.display = 'block';
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(() => showAlert('获取用户信息失败', 'error'));
        }

        function closeUserProfile() {
            document.getElementById('userProfileModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('userProfileModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

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
