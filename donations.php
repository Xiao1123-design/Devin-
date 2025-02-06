<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$donations_sql = "SELECT d.*, u.username 
                 FROM donations d 
                 JOIN users u ON d.donor_id = u.user_id 
                 WHERE d.status = 'available' 
                 ORDER BY d.created_at DESC";
$donations = $conn->query($donations_sql);
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
        <div class="donations-header">
            <h2>Donations Board</h2>
            <a href="#" class="btn-primary" onclick="showDonationForm()">Offer Donation</a>
        </div>
        
        <div class="donations-grid">
            <?php while($donation = $donations->fetch_assoc()): ?>
                <article class="donation-card">
                    <?php if ($donation['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($donation['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($donation['title']); ?>">
                    <?php endif; ?>
                    <div class="donation-info">
                        <h3><?php echo htmlspecialchars($donation['title']); ?></h3>
                        <p class="description"><?php echo htmlspecialchars($donation['description']); ?></p>
                        <p class="donor">
                            Offered by: <?php echo htmlspecialchars($donation['username']); ?>
                        </p>
                        <p class="date">
                            Posted: <?php echo date('M j, Y', strtotime($donation['created_at'])); ?>
                        </p>
                        <a href="chat.php?user=<?php echo $donation['donor_id']; ?>" 
                           class="btn-contact">Contact Donor</a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        
        <!-- Donation Form Modal -->
        <div id="donationModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Offer a Donation</h3>
                <form action="donation_process.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">What are you donating?</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Image (optional)</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <div class="image-preview" id="imagePreview"></div>
                    </div>
                    
                    <button type="submit" class="btn-primary">Post Donation</button>
                </form>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script>
        const modal = document.getElementById('donationModal');
        const closeBtn = document.getElementsByClassName('close')[0];
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        
        function showDonationForm() {
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
        
        imageInput.addEventListener('change', function() {
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
