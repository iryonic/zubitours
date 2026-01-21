<?php require_once '../admin/includes/connection.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 - Page Not Found | Zubi Tours & Holidays</title>
  
  <!-- Favicon -->
  <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/zubilogo.jpg" type="image/jpg" />

  <!-- CSS & Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/styles.css" />

  <style>
    .error-page {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 20px;
      background: radial-gradient(circle at center, #f8fafc 0%, #edf2f7 100%);
    }

    .error-content {
      max-width: 600px;
      padding: 60px 40px;
      background: white;
      border-radius: 24px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.05);
      border: 1px solid rgba(0,0,0,0.05);
    }

    .error-code {
      font-size: 8rem;
      font-weight: 800;
      line-height: 1;
      background: linear-gradient(135deg, #e8862a, #4f46e5);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 20px;
      font-family: var(--body-font);
    }

    .error-title {
      font-size: 2rem;
      color: #1e293b;
      margin-bottom: 15px;
      font-weight: 700;
    }

    .error-message {
      color: #64748b;
      font-size: 1.1rem;
      margin-bottom: 40px;
      line-height: 1.6;
    }

    .home-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: var(--first-color);
      color: #fff;
      padding: 15px 35px;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 10px 20px rgba(232, 134, 42, 0.2);
    }

    .home-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 30px rgba(232, 134, 42, 0.3);
      color: #fff;
    }

    .ri-arrow-right-line {
      transition: transform 0.3s ease;
    }

    .home-btn:hover .ri-arrow-right-line {
      transform: translateX(5px);
    }
  </style>
</head>
<body>

  <main class="error-page">
    <div class="error-content">
      <div class="error-code">404</div>
      <h1 class="error-title">Page Not Found</h1>
      <p class="error-message">Oops! The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
      
      <a href="<?php echo BASE_URL; ?>" class="home-btn">
        Back to Home
        <i class="ri-arrow-right-line"></i>
      </a>
    </div>
  </main>

</body>
</html>
