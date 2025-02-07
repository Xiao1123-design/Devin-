<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if (!$stmt->bind_param("i", $_SESSION['user_id'])) {
    die("Binding parameters failed: " . $stmt->error);
}

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
if ($result === false) {
    die("Getting result failed: " . $stmt->error);
}

$user = $result->fetch_assoc();
if ($user === null) {
    die("User not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - ResellU</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
    <script src="js/rating.js" defer></script>
</head>
<body class="dashboard-body">
    <?php include 'includes/header.php'; ?>
    
    <?php include 'includes/back_button.php'; ?>
    <main class="profile-container">
        <?php
        // Get user ID from URL or use current user's ID
        $user_id = isset($_GET['user']) ? intval($_GET['user']) : $_SESSION['user_id'];
        
        // Fetch user data
        $user_sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($user_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        // Fetch ratings
        $rating_sql = "SELECT AVG(rating) as avg_rating FROM anonymous_ratings WHERE rated_id = ?";
        $stmt = $conn->prepare($rating_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $avg_rating = isset($result['avg_rating']) ? $result['avg_rating'] : 0;
        
        $ratings_sql = "SELECT rating, comment, DATE(created_at) as rating_date 
                       FROM anonymous_ratings 
                       WHERE rated_id = ? 
                       ORDER BY created_at DESC";
        $stmt = $conn->prepare($ratings_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $ratings = $stmt->get_result();
        ?>
        
        <div class="profile-header">
            <h2><?php echo $user_id === $_SESSION['user_id'] ? 'Profile Settings' : htmlspecialchars($user['username']) . "'s Profile"; ?></h2>
        </div>
        
        <div class="rating-section">
            <h3>用户评分</h3>
            <div class="rating-stats">
                <div class="average-rating">
                    <?php echo number_format($avg_rating, 1); ?> / 5.0
                </div>
                <?php if ($user_id !== $_SESSION['user_id']): ?>
                    <button onclick="showRatingForm()" class="btn-primary">评价用户</button>
                <?php endif; ?>
            </div>
            <div id="rating-list">
                <?php while($rating = $ratings->fetch_assoc()): ?>
                    <div class="rating-item">
                        <div class="rating-stars">
                            <?php echo str_repeat('★', $rating['rating']) . str_repeat('☆', 5-$rating['rating']); ?>
                        </div>
                        <div class="rating-comment">
                            <?php echo htmlspecialchars($rating['comment']); ?>
                        </div>
                        <div class="rating-date">
                            <?php echo $rating['rating_date']; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <!-- Rating Modal -->
        <div id="ratingModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>评价用户</h3>
                <form id="ratingForm" onsubmit="submitRating(event)">
                    <input type="hidden" name="rated_id" value="<?php echo $user_id; ?>">
                    <div class="form-group">
                        <label>评分</label>
                        <div class="star-rating">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" name="rating" value="<?php echo $i; ?>" required>
                                <label>★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>评价内容</label>
                        <textarea name="comment" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">提交评价</button>
                </form>
            </div>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">
                <?php 
                    switch($_GET['success']) {
                        case 'profile_updated':
                            echo "Profile updated successfully!";
                            break;
                        case 'password_updated':
                            echo "Password updated successfully!";
                            break;
                    }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert error">
                <?php 
                    switch($_GET['error']) {
                        case 'current_password':
                            echo "Current password is incorrect";
                            break;
                        case 'password_match':
                            echo "New passwords do not match";
                            break;
                        case 'update_failed':
                            echo "Failed to update profile";
                            break;
                    }
                ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-sections">
            <section class="profile-info">
                <h3>Personal Information</h3>
                <form action="profile_update.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="nationality">Nationality</label>
                        <input type="text" id="nationality" name="nationality" 
                               value="<?php echo htmlspecialchars(isset($user['nationality']) ? $user['nationality'] : ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" id="age" name="age" min="16" max="120" 
                               value="<?php echo htmlspecialchars(isset($user['age']) ? $user['age'] : ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="">Prefer not to say</option>
                            <option value="male" <?php echo (isset($user['gender']) && $user['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo (isset($user['gender']) && $user['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo (isset($user['gender']) && $user['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars(isset($user['address']) ? $user['address'] : ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">Update Profile</button>
                </form>
            </section>
            
            <section class="password-change">
                <h3>Change Password</h3>
                <form action="password_update.php" method="POST">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn-primary">Change Password</button>
                </form>
            </section>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
