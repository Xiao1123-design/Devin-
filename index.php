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
    <link rel="stylesheet" href="css/alerts.css">
    <script src="js/alerts.js"></script>
</head>
<body class="auth-page">
    <div class="login-container">
        <h1>Welcome to ResellU</h1>
        <div class="login-box">
            <h2>Login</h2>
            <form id="loginForm" onsubmit="handleLogin(event)">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn-primary">Login</button>
            </form>
            
            <script>
            async function handleLogin(event) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);
                
                try {
                    const response = await fetch('login_process.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => window.location.href = 'dashboard.php', 1000);
                    } else {
                        showAlert(data.message, 'error');
                    }
                } catch (error) {
                    showAlert('An error occurred. Please try again.', 'error');
                }
            }
            
            // Add default admin user if not exists
            fetch('setup_admin.php');
            </script>
            <p>New to ResellU? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
