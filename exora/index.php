<?php
session_start();
include '../admin/includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    // Sanitize input
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $password = trim($_POST['password']); // No need to sanitize password, we check it with password_verify

    // Prepare statement
    if ($stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?")) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Set session  
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];

                setcookie("username", $user['username'], time() + (7 * 24 * 60 * 60), "/", "", false, true);

                header("Location: ../admin/adminpannel.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "User not found.";
        }

        $stmt->close();
    } else {
        $error = "Database error. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dark .glass-effect {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translate(0, 0px);
            }

            50% {
                transform: translate(0, -10px);
            }

            100% {
                transform: translate(0, 0px);
            }
        }

        .gradient-bg {
            background: linear-gradient(-45deg, #8952ee, #3c8ce7, #23a6d5, #7f23d5);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</head>

<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    <div class="max-w-md w-full">

        <!-- Login Card -->
        <div class="glass-effect rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-20 h-20 rounded-full bg-white dark:bg-gray-800 mx-auto mb-4 flex items-center justify-center floating">
                        <i class="fas fa-lock text-3xl text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Admin Portal</h1>
                    <p class="text-gray-200 mt-2">Sign in to access the dashboard</p>
                </div>

                <!-- Real-time Clock -->
                <div class="text-center mb-6">
                    <div id="clock" class="text-white text-lg font-medium"></div>
                </div>

                <!-- Login Form -->
                <form class="space-y-6" method="post" action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>'>
                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/80 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-800 dark:text-white placeholder-gray-500 dark:placeholder-gray-400" placeholder="Username" name="username">
                        </div>
                    </div>

                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                            <input type="password" class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/80 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-800 dark:text-white placeholder-gray-500 dark:placeholder-gray-400" placeholder="Password" name="password">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-white">Remember me</label>

                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-medium text-indigo-200 hover:text-white transition-colors">Forgot password?</a>
                        </div>
                    </div>

                    <div>
                        <input type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors" value="Sign In" name="login">                       
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-8 py-4 bg-black/20 text-center">
                <p class="text-xs text-gray-300">
                    &copy; <span><?php echo date('Y'); ?></span> Exora Panel. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Real-time Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
            const dateString = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            document.getElementById('clock').innerHTML = `
                <div class="text-2xl font-bold mb-1">${timeString}</div>
                <div class="text-sm">${dateString}</div>
            `;
        }

        // Update clock immediately and then every second
        updateClock();
        setInterval(updateClock, 1000);

        // Dark Mode Toggle
        const themeToggle = document.getElementById('themeToggle');
        const icon = themeToggle.querySelector('i');

        // Check for saved theme preference or default to light
        if (localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        } else {
            document.documentElement.classList.remove('dark');
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        }

        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');

            if (document.documentElement.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                localStorage.setItem('theme', 'light');
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });
    </script>
</body>

</html>