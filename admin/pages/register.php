<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'travel_db';
$username = 'root';
$password = '';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get form data
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $username_input = trim($_POST['username']);
        $password_input = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validation
        if (empty($name)) {
            $errors[] = "Name is required";
        }
        
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        }
        
        if (empty($username_input)) {
            $errors[] = "Username is required";
        } elseif (strlen($username_input) < 3) {
            $errors[] = "Username must be at least 3 characters";
        }
        
        if (empty($password_input)) {
            $errors[] = "Password is required";
        } elseif (strlen($password_input) < 6) {
            $errors[] = "Password must be at least 6 characters";
        }
        
        if ($password_input !== $confirm_password) {
            $errors[] = "Passwords do not match";
        }
        
        // Check if username or email already exists
        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = :email OR username = :username");
            $stmt->execute(['email' => $email, 'username' => $username_input]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Username or email already exists";
            }
        }
        
        // Create admin if no errors
        if (empty($errors)) {
            $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("
                INSERT INTO admins (name, email, username, password) 
                VALUES (:name, :email, :username, :password)
            ");
            
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'username' => $username_input,
                'password' => $hashed_password
            ]);
            
            $success = "Admin registered successfully! You can now login.";
        }
        
    } catch(PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin - TravelEase</title>
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
        
        .register-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .register-box {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .register-logo i {
            font-size: 2.5rem;
            color: white;
        }
        
        .register-header h1 {
            font-size: 1.8rem;
            color: #1e293b;
            margin-bottom: 10px;
        }
        
        .register-header p {
            color: #64748b;
            font-size: 0.95rem;
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
        
        .register-btn {
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
        
        .register-btn:hover {
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
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #64748b;
        }
        
        .login-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .register-box {
                padding: 30px 20px;
            }
            
            .register-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <div class="register-logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Register New Admin</h1>
                <p>Create a new admin account</p>
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
            
            <form method="POST" action="" id="registerForm">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="name" name="name" 
                               placeholder="Enter full name" 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                               required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" 
                               placeholder="Enter email address" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                               required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <i class="fas fa-at"></i>
                        <input type="text" id="username" name="username" 
                               placeholder="Choose username" 
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                               required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" 
                               placeholder="Enter password" required>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               placeholder="Confirm password" required>
                    </div>
                    <div class="password-match" id="passwordMatch"></div>
                </div>
                
                <button type="submit" class="register-btn" id="submitBtn">
                    Register Admin
                </button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="../index.php">Login here</a>
            </div>
        </div>
    </div>

    <script>
        // Password strength checker
        const passwordInput = document.getElementById('password');
        const passwordStrength = document.getElementById('passwordStrength');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordMatch = document.getElementById('passwordMatch');
        const submitBtn = document.getElementById('submitBtn');
        
        passwordInput.addEventListener('input', function() {
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
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword.length === 0) {
                passwordMatch.innerHTML = '';
                return;
            }
            
            if (password === confirmPassword) {
                passwordMatch.innerHTML = '<span class="strength-strong">Passwords match ✓</span>';
            } else {
                passwordMatch.innerHTML = '<span class="strength-weak">Passwords do not match ✗</span>';
            }
        }
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long');
                passwordInput.focus();
                return false;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                confirmPasswordInput.focus();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>