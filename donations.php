<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch available donations
$donations_sql = "SELECT d.*, u.username, u.user_type 
                 FROM donations d 
                 JOIN users u ON d.donor_id = u.user_id 
                 WHERE d.status = 'available' 
                 ORDER BY d.created_at DESC";
$donations = $conn->query($donations_sql);
if (!$donations) {
    die("Query failed: " . $conn->error);
}

// Fetch donation requests
$requests_sql = "SELECT d.*, u.username, u.user_type 
                FROM donations d 
                JOIN users u ON d.donor_id = u.user_id 
                WHERE d.status = 'available' 
                ORDER BY d.created_at DESC";
$requests = $conn->query($requests_sql);
if (!$requests) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations - ResellU</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/donations.css">
</head>
<body class="dashboard-body">
    <?php include 'includes/header.php'; ?>
    
    <?php include 'includes/back_button.php'; ?>
    <main class="donations-container">
        <div class="donation-tabs">
            <button class="tab active" data-tab="give">我可以捐赠</button>
            <button class="tab" data-tab="need">我需要捐赠</button>
        </div>

        <!-- I Can Donate Section -->
        <section id="give" class="tab-content active">
            <div class="section-header">
                <h2>可捐赠物品</h2>
                <a href="#" class="btn-primary" onclick="showDonationForm()">发布捐赠</a>
            </div>
            
            <div class="donations-grid">
                <?php while($donation = $donations->fetch_assoc()): ?>
                    <a href="donation.php?id=<?php echo $donation['donation_id']; ?>" class="donation-card">
                        <?php if ($donation['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($donation['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($donation['title']); ?>">
                        <?php endif; ?>
                        <div class="donation-info">
                            <h3><?php echo htmlspecialchars($donation['title']); ?></h3>
                            <p class="category"><?php echo htmlspecialchars($donation['category']); ?></p>
                            <p class="donor">捐赠者: <?php echo htmlspecialchars($donation['username']); ?></p>
                            <p class="user-type"><?php echo htmlspecialchars($donation['user_type']); ?></p>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- I Need Donations Section -->
        <section id="need" class="tab-content">
            <div class="section-header">
                <h2>捐赠请求</h2>
                <a href="#" class="btn-primary" onclick="showRequestForm()">发布请求</a>
            </div>
            
            <div class="requests-grid">
                <?php while($request = $requests->fetch_assoc()): ?>
                    <div class="request-card">
                        <div class="request-info">
                            <h3><?php echo htmlspecialchars($request['title']); ?></h3>
                            <p class="category"><?php echo htmlspecialchars($request['category']); ?></p>
                            <p class="requester">请求者: <?php echo htmlspecialchars($request['username']); ?></p>
                            <p class="user-type"><?php echo htmlspecialchars($request['user_type']); ?></p>
                            <p class="description"><?php echo htmlspecialchars($request['description']); ?></p>
                            <a href="chat.php?user=<?php echo $request['requester_id']; ?>" class="btn-contact">联系请求者</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
        
        <!-- Donation Form Modal -->
        <div id="donationModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>发布捐赠</h3>
                <form action="donation_process.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">物品名称</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">物品类型</label>
                        <select id="category" name="category" required>
                            <option value="">选择类型</option>
                            <option value="Books">书籍</option>
                            <option value="Electronics">电子产品</option>
                            <option value="Furniture">家具</option>
                            <option value="Clothing">服装</option>
                            <option value="Sports">运动用品</option>
                            <option value="Other">其他</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">详细描述</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">图片</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <div class="image-preview" id="imagePreview"></div>
                    </div>
                    
                    <button type="submit" class="btn-primary">发布捐赠</button>
                </form>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Tab switching
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Update active tab
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Show corresponding content
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });

        // Modal handling
        const donationModal = document.getElementById('donationModal');
        const requestModal = document.getElementById('requestModal');
        const closeBtns = document.getElementsByClassName('close');
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        
        function showDonationForm() {
            donationModal.style.display = 'block';
        }
        
        function showRequestForm() {
            requestModal.style.display = 'block';
        }
        
        Array.from(closeBtns).forEach(btn => {
            btn.onclick = function() {
                donationModal.style.display = 'none';
                requestModal.style.display = 'none';
            }
        });
        
        window.onclick = function(event) {
            if (event.target == donationModal) {
                donationModal.style.display = 'none';
            }
            if (event.target == requestModal) {
                requestModal.style.display = 'none';
            }
        }
        
        imageInput?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
