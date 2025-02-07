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
if ($hot_products === false) {
    die("Query failed: " . $conn->error);
}

// Prepare statement for category products
$cat_products_sql = "SELECT p.*, u.username 
                    FROM products p 
                    JOIN users u ON p.seller_id = u.user_id 
                    WHERE p.status = 'available' AND p.category = ?
                    ORDER BY p.created_at DESC LIMIT 4";
$stmt = $conn->prepare($cat_products_sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if (!$stmt->bind_param("s", $category['category'])) {
    die("Binding parameters failed: " . $stmt->error);
}
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
    <script src="js/carousel.js"></script>
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
        <section class="carousel-section">
            <h2>热销商品</h2>
            <div class="product-carousel" id="productCarousel">
                <?php while($product = $hot_products->fetch_assoc()): ?>
                    <a href="product.php?id=<?php echo $product['product_id']; ?>" class="carousel-item">
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <div class="carousel-info">
                            <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                            <p class="price">¥<?php echo number_format($product['price'], 2); ?></p>
                            <p class="seller">卖家: <?php echo htmlspecialchars($product['username']); ?></p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
            <button class="carousel-control prev" onclick="moveCarousel(-1)">❮</button>
            <button class="carousel-control next" onclick="moveCarousel(1)">❯</button>
        </section>

        <?php
        // Fetch products by category
        $categories_sql = "SELECT DISTINCT category FROM products WHERE status = 'available'";
        $categories = $conn->query($categories_sql);
if (!$categories) {
    die("Query failed: " . $conn->error);
}
        ?>

        <section class="category-section">
            <h2>商品推荐</h2>
            <?php while($category = $categories->fetch_assoc()): ?>
                <?php
                $cat_products_sql = "SELECT p.*, u.username 
                                   FROM products p 
                                   JOIN users u ON p.seller_id = u.user_id 
                                   WHERE p.status = 'available' AND p.category = ?
                                   ORDER BY p.created_at DESC LIMIT 4";
                $stmt = $conn->prepare($cat_products_sql);
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("s", $category['category']);
                if (!$stmt->execute()) {
                    die("Execute failed: " . $stmt->error);
                }
                $cat_products = $stmt->get_result();
                if ($cat_products === false) {
                    die("Get result failed: " . $stmt->error);
                }
                
                if ($cat_products->num_rows > 0):
                ?>
                    <div class="category-group">
                        <h3><?php echo htmlspecialchars($category['category']); ?></h3>
                        <div class="product-grid">
                            <?php while($product = $cat_products->fetch_assoc()): ?>
                                <a href="product.php?id=<?php echo $product['product_id']; ?>" class="product-card">
                                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['title']); ?>">
                                    <div class="product-info">
                                        <h4><?php echo htmlspecialchars($product['title']); ?></h4>
                                        <p class="price">¥<?php echo number_format($product['price'], 2); ?></p>
                                        <p class="category"><?php echo htmlspecialchars($product['category']); ?></p>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </section>

        <?php
        // Fetch latest requests
        $requests_sql = "SELECT r.*, u.username 
                        FROM requests r 
                        JOIN users u ON r.user_id = u.user_id 
                        WHERE r.status = 'active' 
                        ORDER BY r.created_at DESC LIMIT 4";
        $requests = $conn->query($requests_sql);
if (!$requests) {
    die("Query failed: " . $conn->error);
}
        ?>

        <section class="requests-section">
            <h2>最新请求</h2>
            <div class="request-grid">
                <?php while($request = $requests->fetch_assoc()): ?>
                    <div class="request-card">
                        <div class="request-info">
                            <h3><?php echo htmlspecialchars($request['title']); ?></h3>
                            <p class="price">期望价格: ¥<?php echo number_format($request['expected_price'], 2); ?></p>
                            <p class="requester">发布者: <?php echo htmlspecialchars($request['username']); ?></p>
                            <a href="chat.php?user=<?php echo $request['user_id']; ?>" class="btn-contact">联系买家</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <a href="requests.php" class="btn-view-all">查看所有请求</a>
        </section>

        <?php
        // Fetch latest donations
        $donations_sql = "SELECT d.*, u.username 
                         FROM donations d 
                         JOIN users u ON d.donor_id = u.user_id 
                         WHERE d.status = 'available' 
                         ORDER BY d.created_at DESC LIMIT 4";
        $donations = $conn->query($donations_sql);
if (!$donations) {
    die("Query failed: " . $conn->error);
}
        ?>

        <section class="donations-section">
            <h2>最新捐赠</h2>
            <div class="donation-grid">
                <?php while($donation = $donations->fetch_assoc()): ?>
                    <a href="product.php?id=<?php echo $donation['donation_id']; ?>" class="donation-card">
                        <?php if ($donation['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($donation['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($donation['title']); ?>">
                        <?php endif; ?>
                        <div class="donation-info">
                            <h3><?php echo htmlspecialchars($donation['title']); ?></h3>
                            <p class="donor">捐赠者: <?php echo htmlspecialchars($donation['username']); ?></p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
            <a href="donations.php" class="btn-view-all">查看所有捐赠</a>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
