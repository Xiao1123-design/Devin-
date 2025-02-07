<header class="main-header">
    <div class="header-content">
        <a href="dashboard.php" class="logo">
            <img src="<?php echo UPLOAD_URL; ?>logo.jpg" alt="ResellU Logo" class="logo-img">
            ResellU
        </a>
        <nav class="main-nav">
            <a href="dashboard.php">首页</a>
            <a href="requests.php">请求</a>
            <a href="donations.php">捐赠</a>
            <a href="sell.php">卖东西</a>
            <a href="profile.php">个人中心</a>
        </nav>
        <div class="user-menu">
            <a href="profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>
</header>
