<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Registration</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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

    .toggle-eye {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      cursor: pointer;
    }

    .error-box {
      display: none;
      background-color: rgba(255, 82, 82, 0.30);
      border: 1px solid rgba(255, 82, 82, 0.4);
      color: #ffb3b3;
      border-radius: 8px;
      padding: 10px 14px;
      margin-bottom: 1rem;
      text-align: center;
      font-size: 14px;
      animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-5px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
  <div class="max-w-md w-full">
    <div class="glass-effect rounded-2xl shadow-2xl overflow-hidden">
      <div class="p-8">
        <div class="text-center mb-6">
          <div
            class="w-20 h-20 rounded-full bg-white dark:bg-gray-800 mx-auto mb-4 flex items-center justify-center floating">
            <i class="fas fa-user-plus text-3xl text-indigo-600 dark:text-indigo-400"></i>
          </div>
          <h1 class="text-2xl font-bold text-white">Admin Registration</h1>
          <p class="text-gray-200 mt-2">Create your admin account</p>
        </div>

        <!-- Error Box -->
        <div id="errorBox" class="error-box"></div>

        <!-- Clock -->
        <div class="text-center mb-6">
          <div id="clock" class="text-white text-lg font-medium"></div>
        </div>

        <!-- Registration Form -->
        <form id="registerForm" class="space-y-6" method="post" action="../admin/logic/registerdetails.php">
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-id-badge text-gray-400"></i>
            </div>
            <input type="text" name="name" placeholder="Full Name"
              class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/80 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
          </div>

          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-envelope text-gray-400"></i>
            </div>
            <input type="email" name="email" placeholder="Email"
              class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/80 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
          </div>

          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-user text-gray-400"></i>
            </div>
            <input type="text" name="usernames" placeholder="Username"
              class="w-full pl-10 pr-4 py-3 rounded-lg bg-white/80 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
          </div>

          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-key text-gray-400"></i>
            </div>
            <input type="password" id="password" name="password" placeholder="Password"
              class="w-full pl-10 pr-10 py-3 rounded-lg bg-white/80 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
            <i class="fas fa-eye toggle-eye" id="togglePassword"></i>
          </div>

          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-key text-gray-400"></i>
            </div>
            <input type="password" id="confirmPassword" placeholder="Confirm Password"
              class="w-full pl-10 pr-10 py-3 rounded-lg bg-white/80 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 text-gray-800 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
            <i class="fas fa-eye toggle-eye" id="toggleConfirmPassword"></i>
          </div>

          <div class="flex items-center">
            <input id="terms" type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
            <label for="terms" class="ml-2 block text-sm text-white">I agree to the
              <a href="#" class="text-indigo-300 hover:text-white">Terms & Conditions</a></label>
          </div>

          <div>
            <input type="submit" name="register"
              class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
              value="Sign Up">
          </div>
        </form>

        <p class="text-center text-gray-300 text-sm mt-6">
          Already have an account?
          <a href="/exora/index.php" class="text-indigo-300 hover:text-white">Login</a>
        </p>
      </div>
    </div>
  </div>

  <script>
    // Real-time Clock
    function updateClock() {
      const now = new Date();
      const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
      const dateString = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
      document.getElementById('clock').innerHTML = `<div class="text-2xl font-bold mb-1">${timeString}</div><div class="text-sm">${dateString}</div>`;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function () {
      const pass = document.getElementById('password');
      this.classList.toggle('fa-eye-slash');
      pass.type = pass.type === 'password' ? 'text' : 'password';
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
      const pass = document.getElementById('confirmPassword');
      this.classList.toggle('fa-eye-slash');
      pass.type = pass.type === 'password' ? 'text' : 'password';
    });

    // Form Validation with Error Box
    document.getElementById('registerForm').addEventListener('submit', function (e) {
      const name = document.querySelector('[name="name"]').value.trim();
      const email = document.querySelector('[name="email"]').value.trim();
      const username = document.querySelector('[name="username"]').value.trim();
      const password = document.getElementById('password').value.trim();
      const confirmPassword = document.getElementById('confirmPassword').value.trim();
      const terms = document.getElementById('terms');
      const errorBox = document.getElementById('errorBox');

      let errors = [];

      if (!name || !email || !username || !password || !confirmPassword)
        errors.push('Please fill out all fields.');

      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (email && !emailPattern.test(email))
        errors.push('Please enter a valid email address.');

      if (password !== confirmPassword)
        errors.push('Passwords do not match.');

      if (!terms.checked)
        errors.push('You must agree to the Terms & Conditions.');

      if (errors.length > 0) {
        e.preventDefault();
        errorBox.innerHTML = errors.join('<br>');
        errorBox.style.display = 'block';
      } else {
        errorBox.style.display = 'none';
      }
    });
  </script>
</body>

</html>
