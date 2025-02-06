<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch hot products
$hot_products_sql = "SELECT p.*, u.username, COUNT(r.review_id) as review_count, AVG(r.rating) as avg_rating 
                     FROM products p 
                     JOIN users u ON p.seller_id = u.user_id 
                     LEFT JOIN reviews r ON u.user_id = r.reviewed_id 
                     WHERE p.status = 'available' 
                     GROUP BY p.product_id 
                     ORDER BY review_count DESC 
                     LIMIT 6";
$hot_products = $conn->query($hot_products_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ResellU</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="js/search.js"></script>
</head>
<body class="dashboard-body">
    <?php include 'includes/header.php'; ?>
    
    <main class="dashboard-main">
        <div class="search-container">
            <input type="text" id="globalSearch" placeholder="搜索商品..." onkeyup="handleSearch(event)">
            <select id="categoryFilter" onchange="handleSearch()">
                <option value="">所有分类</option>
                <option value="Books">书籍</option>
                <option value="Electronics">电子产品</option>
                <option value="Furniture">家具</option>
                <option value="Clothing">服装</option>
                <option value="Sports">运动用品</option>
                <option value="Other">其他</option>
            </select>
        </div>
        <section class="featured-section">
            <h2>Hot Products</h2>
            <div class="product-grid">
                <?php while($product = $hot_products->fetch_assoc()): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                            <p class="seller">Seller: <?php echo htmlspecialchars($product['username']); ?></p>
                            <a href="product.php?id=<?php echo $product['product_id']; ?>" class="btn-view">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <section class="quick-actions">
            <div class="action-card">
                <h3>Sell an Item</h3>
                <a href="sell.php" class="btn-primary">Post Item</a>
            </div>
            <div class="action-card">
                <h3>Request Item</h3>
                <a href="request.php" class="btn-primary">Make Request</a>
            </div>
            <div class="action-card">
                <h3>Donate Items</h3>
                <a href="donate.php" class="btn-primary">Donate</a>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
