<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['id']);

// Fetch product details with seller info and rating
$product_sql = "SELECT p.*, u.username, u.user_id as seller_id, 
                AVG(r.rating) as seller_rating, COUNT(r.review_id) as review_count
                FROM products p 
                JOIN users u ON p.seller_id = u.user_id 
                LEFT JOIN reviews r ON u.user_id = r.reviewed_id 
                WHERE p.product_id = ?
                GROUP BY p.product_id";
$stmt = $conn->prepare($product_sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: browse.php");
    exit();
}

// Fetch seller reviews
$reviews_sql = "SELECT r.*, u.username 
                FROM reviews r 
                JOIN users u ON r.reviewer_id = u.user_id 
                WHERE r.reviewed_id = ? 
                ORDER BY r.created_at DESC 
                LIMIT 5";
$stmt = $conn->prepare($reviews_sql);
$stmt->bind_param("i", $product['seller_id']);
$stmt->execute();
$reviews = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['title']); ?> - ResellU</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/product.css">
</head>
<body class="dashboard-body">
    <?php include 'includes/header.php'; ?>
    
    <?php include 'includes/back_button.php'; ?>
    <main class="product-container">
        <div class="product-details">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($product['title']); ?>">
            </div>
            
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['title']); ?></h1>
                
                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                
                <div class="seller-info">
                    <h3>Seller Information</h3>
                    <p class="seller-name">
                        <?php echo htmlspecialchars($product['username']); ?>
                        <?php if ($product['seller_rating']): ?>
                            <span class="rating">
                                ★ <?php echo number_format($product['seller_rating'], 1); ?>
                                (<?php echo $product['review_count']; ?> reviews)
                            </span>
                        <?php endif; ?>
                    </p>
                    
                    <?php if ($product['seller_id'] !== $_SESSION['user_id']): ?>
                        <a href="chat.php?user=<?php echo $product['seller_id']; ?>" 
                           class="btn-contact">Contact Seller</a>
                        
                        <?php if (!$product['seller_id'] === $_SESSION['user_id']): ?>
                            <button onclick="showReviewForm()" class="btn-review">Rate Seller</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <div class="product-meta">
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
                    <p><strong>Condition Status:</strong> <?php echo htmlspecialchars($product['condition_status']); ?></p>
                    <p><strong>Listed:</strong> <?php echo date('M j, Y', strtotime($product['created_at'])); ?></p>
                </div>
                
                <div class="product-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
            </div>
        </div>
        
        <?php if ($reviews->num_rows > 0): ?>
            <section class="seller-reviews">
                <h3>Recent Seller Reviews</h3>
                <div class="reviews-list">
                    <?php while($review = $reviews->fetch_assoc()): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <span class="reviewer"><?php echo htmlspecialchars($review['username']); ?></span>
                                <span class="rating">
                                    <?php for($i = 0; $i < $review['rating']; $i++): ?>★<?php endfor; ?>
                                </span>
                            </div>
                            <?php if ($review['comment']): ?>
                                <p class="review-comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                            <?php endif; ?>
                            <span class="review-date">
                                <?php echo date('M j, Y', strtotime($review['created_at'])); ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        <?php endif; ?>
        
        <!-- Review Modal -->
        <div id="reviewModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Rate Seller</h3>
                <form action="review_process.php" method="POST">
                    <input type="hidden" name="reviewed_id" value="<?php echo $product['seller_id']; ?>">
                    
                    <div class="form-group">
                        <label for="rating">Rating</label>
                        <div class="rating-input">
                            <?php for($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?php echo $i; ?>" 
                                       name="rating" value="<?php echo $i; ?>" required>
                                <label for="star<?php echo $i; ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="comment">Comment (optional)</label>
                        <textarea id="comment" name="comment" rows="4"></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">Submit Review</button>
                </form>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script>
        const modal = document.getElementById('reviewModal');
        const closeBtn = document.getElementsByClassName('close')[0];
        
        function showReviewForm() {
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
