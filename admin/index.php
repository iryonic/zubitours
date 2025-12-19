<?php
session_start();

require_once './includes/connection.php';

// Redirect already-logged-in admin to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: adminpannel.php');
    exit();
}





if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
      
        $user = $_POST['username'];
        $pass = $_POST['password'];
        
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = :username");
        $stmt->execute(['username' => $user]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($pass, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_logged_in'] = true;
            
            header('Location: adminpannel.php');
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }


    //if admin  is alreday logged in, redirect to admin panel
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TravelEase</title>
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
        
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        
        .login-box {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .login-logo i {
            font-size: 2.5rem;
            color: white;
        }
        
        .login-header h1 {
            font-size: 1.8rem;
            color: #1e293b;
            margin-bottom: 10px;
        }
        
        .login-header p {
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
        
        .login-btn {
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
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }
        
        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: <?php echo isset($error) ? 'block' : 'none'; ?>;
        }
        
        @media (max-width: 480px) {
            .login-box {
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-plane"></i>
                </div>
                <h1>Zubi Tours</h1>
                <p>Enter your credentials to access the dashboard</p>
            </div>
            
            <?php if(isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>
                
                <button type="submit" class="login-btn">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</body>
</html>