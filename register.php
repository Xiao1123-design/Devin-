<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ResellU</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/alerts.css">
    <script src="js/alerts.js"></script>
    <script>
    async function checkUsername(username) {
        const statusElement = document.getElementById('username-status');
        if (!username) {
            statusElement.textContent = '';
            statusElement.className = 'validation-status';
            return false;
        }
        
        try {
            const response = await fetch('check_username.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username })
            });
            
            const data = await response.json();
            
            if (!data.success) {
                showAlert(data.message, 'error');
                return false;
            }
            
            statusElement.textContent = (data.available ? '✓ ' : '✗ ') + data.message;
            statusElement.className = 'validation-status ' + (data.available ? 'valid' : 'invalid');
            
            if (!data.available) {
                showAlert(data.message, 'error');
            }
            
            return data.available;
        } catch (error) {
            showAlert('验证用户名时出错', 'error');
            return false;
        }
    }

    document.querySelector('form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        if (await checkUsername(username)) {
            this.submit();
        }
    });
    </script>
</head>
<body class="auth-page">
    <div class="login-container">
        <h1>加入 ResellU</h1>
        <div class="login-box">
            <h2>创建账号</h2>
            <form action="register_process.php" method="POST">
                <div class="form-group">
                    <input type="text" name="username" id="username" placeholder="用户名" required onblur="checkUsername(this.value)">
                    <span id="username-status" class="validation-status"></span>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="邮箱" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="密码" required>
                </div>
                <div class="form-group">
                    <select name="user_type" required>
                        <option value="">选择用户类型</option>
                        <option value="student">学生</option>
                        <option value="alumni">校友</option>
                        <option value="teacher">教师</option>
                        <option value="staff">职工</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">注册</button>
            </form>
            <p>已有账号？<a href="index.php">点此登录</a></p>
        </div>
    </div>
</body>
</html>
