<header class="main-header">
    <div class="header-content">
        <a href="dashboard.php" class="logo">ResellU</a>
        <nav class="main-nav">
            <a href="browse.php">Browse</a>
            <a href="requests.php">Requests</a>
            <a href="donations.php">Donations</a>
            <a href="messages.php">Messages</a>
        </nav>
        <div class="user-menu">
            <a href="profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>
</header>
