<?php
session_start();
require_once '../config.php';

// Check if user is admin
$admin_sql = "SELECT user_type FROM users WHERE user_id = ?";
$stmt = $conn->prepare($admin_sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!isset($_SESSION['user_id']) || $user['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch all users
$users_sql = "SELECT * FROM users ORDER BY created_at DESC";
$users = $conn->query($users_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - ResellU</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="dashboard-body">
    <?php include '../includes/header.php'; ?>
    
    <?php include '../includes/back_button.php'; ?>
    <main class="admin-container">
        <div class="admin-header">
            <h2>Admin Panel</h2>
            <button class="btn-primary" onclick="showAddUserForm()">Add New User</button>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">
                <?php 
                    switch($_GET['success']) {
                        case 'user_added':
                            echo "User added successfully!";
                            break;
                        case 'user_updated':
                            echo "User updated successfully!";
                            break;
                        case 'user_deleted':
                            echo "User deleted successfully!";
                            break;
                    }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_type']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td class="actions">
                                <button onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                                        class="btn-edit">Edit</button>
                                <button onclick="deleteUser(<?php echo $user['user_id']; ?>)"
                                        class="btn-delete">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Add/Edit User Modal -->
        <div id="userModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3 id="modalTitle">Add New User</h3>
                <form id="userForm" action="user_process.php" method="POST">
                    <input type="hidden" id="user_id" name="user_id">
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password">
                        <small>(Leave empty to keep current password when editing)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="user_type">User Type</label>
                        <select id="user_type" name="user_type" required>
                            <option value="student">Student</option>
                            <option value="alumni">Alumni</option>
                            <option value="teacher">Teacher</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-primary">Save User</button>
                </form>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    
    <script>
        const modal = document.getElementById('userModal');
        const closeBtn = document.getElementsByClassName('close')[0];
        const form = document.getElementById('userForm');
        
        function showAddUserForm() {
            document.getElementById('modalTitle').textContent = 'Add New User';
            form.reset();
            document.getElementById('user_id').value = '';
            document.getElementById('password').required = true;
            modal.style.display = 'block';
        }
        
        function editUser(user) {
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('user_id').value = user.user_id;
            document.getElementById('username').value = user.username;
            document.getElementById('email').value = user.email;
            document.getElementById('user_type').value = user.user_type;
            document.getElementById('password').required = false;
            modal.style.display = 'block';
        }
        
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = `user_process.php?action=delete&user_id=${userId}`;
            }
        }
        
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
