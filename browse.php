<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT p.*, u.username, AVG(r.rating) as seller_rating 
        FROM products p 
        JOIN users u ON p.seller_id = u.user_id 
        LEFT JOIN reviews r ON u.user_id = r.reviewed_id 
        WHERE p.status = 'available'";

if (!empty($search)) {
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
}
if (!empty($category)) {
    $sql .= " AND p.category = ?";
}

$sql .= " GROUP BY p.product_id ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($search) && !empty($category)) {
    $search_param = "%$search%";
    $stmt->bind_param("sss", $search_param, $search_param, $category);
} elseif (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bind_param("ss", $search_param, $search_param);
} elseif (!empty($category)) {
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$products = $stmt->get_result();

// Get categories
$categories_sql = "SELECT DISTINCT category FROM products ORDER BY category";
$categories = $conn->query($categories_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products - ResellU</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/browse.css">
</head>
<body class="dashboard-body">
    <?php include 'includes/header.php'; ?>
    
    <?php include 'includes/back_button.php'; ?>
    <main class="browse-container">
        <aside class="filters">
            <form action="browse.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search products..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                
                <select name="category">
                    <option value="">All Categories</option>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($cat['category']); ?>"
                                <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <button type="submit" class="btn-primary">Search</button>
            </form>
        </aside>
        
        <section class="products-grid">
            <?php while($product = $products->fetch_assoc()): ?>
                <article class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>">
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <p class="seller">
                            Seller: <?php echo htmlspecialchars($product['username']); ?>
                            <?php if ($product['seller_rating']): ?>
                                <span class="rating">★ <?php echo number_format($product['seller_rating'], 1); ?></span>
                            <?php endif; ?>
                        </p>
                        <p class="category"><?php echo htmlspecialchars($product['category']); ?></p>
                        <div class="product-actions">
                            <a href="product.php?id=<?php echo $product['product_id']; ?>" 
                               class="btn-view">View Details</a>
                            <a href="chat.php?user=<?php echo $product['seller_id']; ?>" 
                               class="btn-contact">Contact Seller</a>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
            
            <?php if ($products->num_rows === 0): ?>
                <div class="no-results">
                    <p>No products found matching your criteria.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
