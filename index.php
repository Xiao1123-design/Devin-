<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResellU - Campus Trading Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Welcome to ResellU</h1>
        <div class="login-box">
            <h2>Login</h2>
            <form action="login_process.php" method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn-primary">Login</button>
            </form>
            <p>New to ResellU? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
