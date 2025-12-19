<?php
session_start();
require_once '../includes/connection.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}



$errors = [];
$success = '';

try {
   
    // Get current admin info
    $admin_id = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT * FROM admins WHERE id = :id");
    $stmt->execute(['id' => $admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validation
        if (empty($current_password)) {
            $errors[] = "Current password is required";
        }
        
        if (empty($new_password)) {
            $errors[] = "New password is required";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters";
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        }
        
        // Verify current password
        if (empty($errors) && !password_verify($current_password, $admin['password'])) {
            $errors[] = "Current password is incorrect";
        }
        
        // Check if new password is same as old
        if (empty($errors) && password_verify($new_password, $admin['password'])) {
            $errors[] = "New password cannot be the same as current password";
        }
        
        // Update password if no errors
        if (empty($errors)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("UPDATE admins SET password = :password WHERE id = :id");
            $stmt->execute([
                'password' => $hashed_password,
                'id' => $admin_id
            ]);
            
            $success = "Password updated successfully!";
            
            // Clear form
            $_POST = [];
        }
    }
    
} catch(PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - TravelEase Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .password-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .password-box {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .password-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .password-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .password-logo i {
            font-size: 2.5rem;
            color: white;
        }
        
        .password-header h1 {
            font-size: 1.8rem;
            color: #1e293b;
            margin-bottom: 10px;
        }
        
        .password-header p {
            color: #64748b;
            font-size: 0.95rem;
        }
        
        .admin-info {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }
        
        .admin-info p {
            margin: 5px 0;
            color: #475569;
        }
        
        .admin-info strong {
            color: #1e293b;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1e293b;
            font-weight: 500;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .input-with-icon input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 1rem;
        }
        
        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }
        
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .alert-success {
            background: #dcfce7;
            color: #16a34a;
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 0.85rem;
        }
        
        .strength-weak { color: #dc2626; }
        .strength-medium { color: #f59e0b; }
        .strength-strong { color: #16a34a; }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
            color: #64748b;
        }
        
        .back-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .password-box {
                padding: 30px 20px;
            }
            
            .password-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="password-container">
        <div class="password-box">
            <div class="password-header">
                <div class="password-logo">
                    <i class="fas fa-key"></i>
                </div>
                <h1>Change Password</h1>
                <p>Update your admin account password</p>
            </div>
            
            <div class="admin-info">
                <p><strong>Admin:</strong> <?php echo htmlspecialchars($admin['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($admin['username']); ?></p>
            </div>
            
            <?php if(!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php if($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="passwordForm">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="current_password" name="current_password" 
                               placeholder="Enter current password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="new_password" name="new_password" 
                               placeholder="Enter new password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               placeholder="Confirm new password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-match" id="passwordMatch"></div>
                </div>
                
                <button type="submit" class="submit-btn" id="submitBtn">
                    Change Password
                </button>
            </form>
            
            <div class="back-link">
                <a href="adminpannel.php">← Back to Dashboard</a>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const toggleBtn = input.nextElementSibling;
            const icon = toggleBtn.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Password strength checker
        const newPasswordInput = document.getElementById('new_password');
        const passwordStrength = document.getElementById('passwordStrength');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordMatch = document.getElementById('passwordMatch');
        const currentPasswordInput = document.getElementById('current_password');
        
        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = '';
            let strengthClass = '';
            
            if (password.length === 0) {
                strength = '';
            } else if (password.length < 6) {
                strength = 'Weak (min 6 characters)';
                strengthClass = 'strength-weak';
            } else if (password.length < 10) {
                strength = 'Medium';
                strengthClass = 'strength-medium';
            } else if (/[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) {
                strength = 'Strong';
                strengthClass = 'strength-strong';
            } else {
                strength = 'Medium';
                strengthClass = 'strength-medium';
            }
            
            if (strength) {
                passwordStrength.innerHTML = `<span class="${strengthClass}">${strength}</span>`;
            } else {
                passwordStrength.innerHTML = '';
            }
            
            checkPasswordMatch();
        });
        
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        
        function checkPasswordMatch() {
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword.length === 0) {
                passwordMatch.innerHTML = '';
                return;
            }
            
            if (newPassword === confirmPassword) {
                passwordMatch.innerHTML = '<span class="strength-strong">Passwords match ✓</span>';
            } else {
                passwordMatch.innerHTML = '<span class="strength-weak">Passwords do not match ✗</span>';
            }
        }
        
        // Form validation
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPassword = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const currentPassword = currentPasswordInput.value;
            
            if (currentPassword.length === 0) {
                e.preventDefault();
                alert('Current password is required');
                currentPasswordInput.focus();
                return false;
            }
            
            if (newPassword.length < 6) {
                e.preventDefault();
                alert('New password must be at least 6 characters long');
                newPasswordInput.focus();
                return false;
            }
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match');
                confirmPasswordInput.focus();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>