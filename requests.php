<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$requests_sql = "SELECT r.*, u.username 
                FROM requests r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.status = 'active' 
                ORDER BY r.created_at DESC";
$requests = $conn->query($requests_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Board - ResellU</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/requests.css">
</head>
<body class="dashboard-body">
    <?php include 'includes/header.php'; ?>
    
    <main class="requests-container">
        <div class="requests-header">
            <h2>Request Board</h2>
            <a href="#" class="btn-primary" onclick="showRequestForm()">Post Request</a>
        </div>
        
        <div class="requests-grid">
            <?php while($request = $requests->fetch_assoc()): ?>
                <article class="request-card">
                    <div class="request-info">
                        <h3><?php echo htmlspecialchars($request['title']); ?></h3>
                        <p class="description"><?php echo htmlspecialchars($request['description']); ?></p>
                        <p class="expected-price">
                            Expected Price: $<?php echo number_format($request['expected_price'], 2); ?>
                        </p>
                        <p class="requester">
                            Posted by: <?php echo htmlspecialchars($request['username']); ?>
                        </p>
                        <p class="date">
                            Posted: <?php echo date('M j, Y', strtotime($request['created_at'])); ?>
                        </p>
                        <a href="chat.php?user=<?php echo $request['user_id']; ?>" 
                           class="btn-contact">Contact Requester</a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        
        <!-- Request Form Modal -->
        <div id="requestModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Post a Request</h3>
                <form action="request_process.php" method="POST">
                    <div class="form-group">
                        <label for="title">What are you looking for?</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="expected_price">Expected Price ($)</label>
                        <input type="number" id="expected_price" name="expected_price" 
                               step="0.01" min="0" required>
                    </div>
                    
                    <button type="submit" class="btn-primary">Post Request</button>
                </form>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script>
        const modal = document.getElementById('requestModal');
        const closeBtn = document.getElementsByClassName('close')[0];
        
        function showRequestForm() {
            modal.style.display = 'block';
        }
        
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
