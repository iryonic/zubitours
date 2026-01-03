<?php
session_start();
require_once './includes/connection.php';

// Redirect to login if NOT authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

try {
    // Fetch dashboard statistics
    $stats = [];
    $recent_bookings = [];
    $new_messages_count = 0;
    $pending_bookings_count = 0;

    // Total Users (assuming from package_bookings table)
    if ($stmt = $conn->prepare("SELECT COUNT(DISTINCT customer_email) as total_users FROM package_bookings")) {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['total_users'] = $row['total_users'] ?? 0;
        $stmt->close();
    }

    // Total Bookings
    if ($stmt = $conn->prepare("SELECT COUNT(*) as total_bookings FROM package_bookings")) {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['total_bookings'] = $row['total_bookings'] ?? 0;
        $stmt->close();
    }

    // Total Revenue
    if ($stmt = $conn->prepare("SELECT SUM(total_amount) as total_revenue FROM package_bookings WHERE payment_status = 'paid'")) {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['total_revenue'] = $row['total_revenue'] ? $row['total_revenue'] : 0;
        $stmt->close();
    }

    // Total Packages
    if ($stmt = $conn->prepare("SELECT COUNT(*) as total_packages FROM packages WHERE is_active = 1")) {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['total_packages'] = $row['total_packages'] ?? 0;
        $stmt->close();
    }

    // Fetch recent package bookings
    if ($stmt = $conn->prepare("SELECT pb.*, p.package_name FROM package_bookings pb LEFT JOIN packages p ON pb.package_id = p.id ORDER BY pb.booked_at DESC LIMIT 5")) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $recent_bookings[] = $row;
        }
        $stmt->close();
    }

    // Fetch recent car bookings (if table exists)
    $recent_car_bookings = [];
    // Uncomment if car_bookings table exists
    /*
    if ($stmt = $conn->prepare("SELECT cb.*, cr.car_name FROM car_bookings cb LEFT JOIN car_rentals cr ON cb.car_id = cr.id ORDER BY cb.booking_date DESC LIMIT 5")) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $recent_car_bookings[] = $row;
        }
        $stmt->close();
    }
    */

    // Fetch recent contact messages
    $recent_messages = [];
    if ($stmt = $conn->prepare("SELECT * FROM contact_messages WHERE status = 'new' ORDER BY created_at DESC LIMIT 10")) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $recent_messages[] = $row;
        }
        $stmt->close();
    }

    // Count new messages for badge
    if ($stmt = $conn->prepare("SELECT COUNT(*) as new_messages FROM contact_messages WHERE status = 'new'")) {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $new_messages_count = $row['new_messages'] ?? 0;
        $stmt->close();
    }

    // Count pending package bookings for badge
    if ($stmt = $conn->prepare("SELECT COUNT(*) as pending_bookings FROM package_bookings WHERE booking_status = 'pending'")) {
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $pending_bookings_count = $row['pending_bookings'] ?? 0;
        $stmt->close();
    }
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get admin name from session
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$first_letter = strtoupper(substr($admin_name, 0, 1));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Variables */
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #93c5fd;
            --secondary-color: #64748b;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --error-color: #ef4444;
            --sidebar-width: 280px;
            --header-height: 80px;
            --card-radius: 16px;
            --transition: all 0.3s ease;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --border-color: #e2e8f0;
        }

        .dark-mode {
            --card-bg: #1f2937;
            --text-primary: #f3f4f6;
            --text-secondary: #d1d5db;
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --border-color: #374151;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            transition: var(--transition);
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-secondary);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow);
            border-right: 1px solid var(--border-color);
            z-index: 1000;
        }

        .sidebar-header {
            padding: 24px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            height: var(--header-height);
        }

        .sidebar-logo {
            width: 45px;
            height: 45px;
            background: var(--primary-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .sidebar-logo i {
            font-size: 1.5rem;
            color: white;
        }

        .sidebar-title {
            font-size: 1.3rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-menu {
            padding: 24px 0;
            flex: 1;
            overflow-y: auto;
        }

        .menu-section {
            margin-bottom: 32px;
        }

        .menu-label {
            padding: 0 24px;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 16px;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
            border-left: 4px solid transparent;
            margin: 4px 0;
            border-radius: 0 12px 12px 0;
        }

        .menu-item:hover,
        .menu-item.active {
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.1) 0%, rgba(37, 99, 235, 0.05) 100%);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        .menu-item i {
            margin-right: 16px;
            font-size: 1.3rem;
        }

        .menu-item span {
            flex: 1;
            font-weight: 500;
        }

        .menu-badge {
            background: var(--primary-color);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            background: var(--bg-secondary);
        }

        .admin-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-weight: 600;
            color: white;
            font-size: 1.1rem;
        }

        .admin-info {
            flex: 1;
        }

        .admin-name {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .admin-role {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
        }

        .header {
            height: var(--header-height);
            background: var(--bg-secondary);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .page-title {
            font-size: 1.7rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-box {
            position: relative;
        }

        .search-input {
            padding: 12px 16px 12px 48px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            width: 280px;
            font-size: 0.95rem;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .header-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            position: relative;
            cursor: pointer;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .header-icon:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--error-color);
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 600;
            border: 2px solid var(--bg-secondary);
        }

        .theme-toggle {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* Dashboard Content */
        .content {
            padding: 32px 10px;
        }

        .welcome-header {
            margin-bottom: 30px;
            padding: 25px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: var(--card-radius);
            color: white;
            box-shadow: var(--shadow);
        }

        .welcome-header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .overview-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .overview-card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: var(--transition);
        }

        .overview-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .overview-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .icon-users {
            background: rgba(37, 99, 235, 0.15);
            color: var(--primary-color);
        }

        .icon-bookings {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
        }

        .icon-revenue {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning-color);
        }

        .icon-packages {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error-color);
        }

        .overview-info h3 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 5px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .overview-info p {
            color: var(--text-secondary);
        }

        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .chart-header h3 {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .chart-placeholder {
            height: 300px;
            background: var(--bg-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            flex-direction: column;
        }

        .chart-placeholder i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.7;
        }

        .recent-activity {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
        }

        .recent-activity h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 25px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.4rem;
        }

        .activity-content {
            flex: 1;
        }

        .activity-content h4 {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .activity-content p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .activity-time {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .quick-action {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
        }

        .quick-action:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
        }

        .quick-action-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            background: rgba(37, 99, 235, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: var(--primary-color);
        }

        .quick-action h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .quick-action p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .charts-section {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            :root {
                --sidebar-width: 80px;
            }

            .sidebar-title,
            .menu-item span,
            .menu-label,
            .admin-info,
            .menu-badge {
                display: none;
            }

            .sidebar-header {
                justify-content: center;
                padding: 20px;
            }

            .sidebar-logo {
                margin-right: 0;
            }

            .menu-item {
                justify-content: center;
                padding: 16px;
                border-left: none;
                border-radius: 12px;
                margin: 6px 12px;
            }

            .menu-item i {
                margin-right: 0;
                font-size: 1.5rem;
            }

            .sidebar-footer {
                justify-content: center;
                padding: 16px;
            }

            .admin-avatar {
                margin-right: 0;
            }

            .main-content {
                margin-left: 80px;
                width: calc(100% - 80px);
            }

            .search-input {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 0 20px;
            }

            .search-box {
                display: none;
            }

            .content {
                padding: 20px;
            }

            .header-actions {
                gap: 12px;
            }

            .header-icon {
                width: 40px;
                height: 40px;
            }

            .overview-cards {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .activity-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .activity-icon {
                margin-right: 0;
            }

            .activity-time {
                align-self: flex-end;
            }
        }
    </style>
</head>

<body>


    <!-- Sidebar -->

    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="../assets/img/zubilogo.jpg" alt="zubilogo" id="logo" style="height: 50px; width: auto ;border-radius: 20px;">
            </div>
            <div class="sidebar-title">Zubi Tours</div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-label">Main</div>
                <a href="../admin/adminpannel.php" class="menu-item active">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>


            </div>

            <div class="menu-section">
                <div class="menu-label">Manage Website</div>
                <a href="../admin/pages/manage-homepage.php" class="menu-item ">
                    <i class="ri-home-4-line"></i>
                    <span>Homepage</span>
                </a>
                <a href="../admin/pages/manage-callbacks.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'manage-callbacks.php' ? 'active' : ''; ?>">
                    <i class="ri-phone-line"></i> <span>Callback Requests</span>
                </a>

                <a href="../admin/pages/manage-destinations.php" class="menu-item">
                    <i class="ri-map-2-line"></i>
                    <span>Destinations</span>
                </a>
                <a href="../admin/pages/manage-packages.php" class="menu-item">
                    <i class="ri-briefcase-4-line"></i>
                    <span>Packages</span>
                </a>
                <a href="../admin/pages/manage-gallery.php" class="menu-item">
                    <i class="ri-gallery-line"></i>
                    <span>Gallery</span>
                </a>
                <a href="../admin/pages/manage-car-rentals.php" class="menu-item">
                    <i class="ri-car-line"></i>
                    <span>Car Rentals</span>
                </a>
                <a href="../admin/pages/manage-contacts.php" class="menu-item ">
                    <i class="ri-mail-line"></i>
                    <span>Contact</span>

                </a>
            </div>



            <div class="menu-section">
                <div class="menu-label">Settings</div>
                <a href="../admin/pages/register.php" class="menu-item">
                    <i class="ri-user-add-line"></i>
                    <span>Register Admin</span>
                </a>
                <a href="../admin/pages/change-password.php" class="menu-item">
                    <i class="ri-settings-3-line"></i>
                    <span>Change Password</span>
                </a>
                <a href="./logout.php" class="menu-item">
                    <i class="ri-logout-box-line"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar"> <img src="../assets/img/zubilogo.jpg" alt="zubilogo" id="logo" style="height: 50px; width: auto ;border-radius: 20px;"></div>
                <div class="admin-info">
                    <div class="admin-name">Admin User</div>
                    <div class="admin-role">Super Admin</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <h1 class="page-title">Dashboard</h1>

        </header>

        <!-- Content -->
        <div class="content">
            <!-- Welcome Header -->
            <div class="welcome-header">
                <h1>Welcome back, Admin!</h1>
                <p>Here's what's happening with your business today.</p>
            </div>

            <!-- Overview Cards -->
            <div class="overview-cards">
                <div class="overview-card">
                    <div class="overview-icon icon-users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="overview-info">
                        <h3><?php echo number_format($stats['total_users']); ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>

                <div class="overview-card">
                    <div class="overview-icon icon-bookings">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="overview-info">
                        <h3><?php echo number_format($stats['total_bookings']); ?></h3>
                        <p>Bookings</p>
                    </div>
                </div>

                <div class="overview-card">
                    <div class="overview-icon icon-revenue">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="overview-info">
                        <h3>$<?php echo number_format($stats['total_revenue'], 0); ?></h3>
                        <p>Revenue</p>
                    </div>
                </div>

                <div class="overview-card">
                    <div class="overview-icon icon-packages">
                        <i class="fas fa-suitcase"></i>
                    </div>
                    <div class="overview-info">
                        <h3><?php echo number_format($stats['total_packages']); ?></h3>
                        <p>Packages</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="recent-activity">
                <h3>Recent Bookings</h3>
                <div class="activity-list">
                    <?php foreach ($recent_bookings as $booking): ?>
                        <div class="activity-item">
                            <div class="activity-icon" style="background: rgba(16, 185, 129, 0.15); color: var(--success-color);">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="activity-content">
                                <h4><?php echo htmlspecialchars($booking['customer_name']); ?></h4>
                                <p>
                                    <?php echo htmlspecialchars($booking['package_name'] ?? 'Package'); ?> -
                                    $<?php echo number_format($booking['total_amount'], 2); ?>
                                </p>
                            </div>
                            <div class="activity-time">
                                <?php
                                $timeAgo = strtotime($booking['booked_at']);
                                $now = time();
                                $diff = $now - $timeAgo;

                                if ($diff < 60) {
                                    echo $diff . ' seconds ago';
                                } elseif ($diff < 3600) {
                                    echo floor($diff / 60) . ' minutes ago';
                                } elseif ($diff < 86400) {
                                    echo floor($diff / 3600) . ' hours ago';
                                } else {
                                    echo floor($diff / 86400) . ' days ago';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <h3 style="margin-bottom: 20px; font-size: 1.3rem; font-weight: 700;">Quick Actions</h3>
            <div class="quick-actions">
                <a href="../admin/pages/manage-packages.php" class="quick-action">
                    <div class="quick-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h4>Add Package</h4>
                    <p>Create new travel package</p>
                </a>

                <a href="../admin/pages/manage-destinations.php" class="quick-action">
                    <div class="quick-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h4>Add Destinations</h4>
                    <p>Create new travel package</p>
                </a>

                <a href="../admin/pages/manage-gallery.php" class="quick-action">
                    <div class="quick-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h4>Add Image</h4>
                    <p>post another gallery Image </p>
                </a>

                <a href="../admin/pages/manage-car-rentals.php" class="quick-action">
                    <div class="quick-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h4>Add Car</h4>
                    <p>Add new car to your Car rentals</p>
                </a>

                <a href="../admin/pages/Change-password.php" class="quick-action">
                    <div class="quick-action-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h4>Change Password</h4>
                    <p>Configure system Authentication settings</p>
                </a>
            </div>
        </div>
    </main>

    <script>
        // Dark/Light Mode Toggle
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('i');

        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');

            if (document.body.classList.contains('dark-mode')) {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }

            // Save theme preference to localStorage
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode);
        });

        // Check for saved theme preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }

        // Menu Active State
        const menuItems = document.querySelectorAll('.menu-item');
        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                menuItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Search functionality
        const searchInput = document.querySelector('.search-input');
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    alert(`Searching for: ${searchTerm}`);
                    // Implement actual search functionality here
                }
            }
        });

        // Quick Action Cards Hover
        const quickActions = document.querySelectorAll('.quick-action');
        quickActions.forEach(action => {
            action.addEventListener('mouseenter', function() {
                const icon = this.querySelector('.quick-action-icon i');
                icon.style.transform = 'scale(1.1)';
                icon.style.transition = 'transform 0.3s ease';
            });

            action.addEventListener('mouseleave', function() {
                const icon = this.querySelector('.quick-action-icon i');
                icon.style.transform = 'scale(1)';
            });
        });

        // Auto-refresh notifications every 30 seconds
        setInterval(() => {
            // You can implement AJAX call here to refresh notification counts
            console.log('Refreshing notifications...');
        }, 30000);
    </script>
</body>

</html>