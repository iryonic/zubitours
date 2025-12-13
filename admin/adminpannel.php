<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Zubi Tours</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

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

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            transition: var(--transition);
        }

        body::-webkit-scrollbar {
            width: 8px;
            background: transparent;

        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-secondary);
            color: var(--text-primary);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            z-index: 1000;
            box-shadow: var(--shadow);
            border-right: 1px solid var(--border-color);
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 8px;
            background: transparent;
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
            transition: var(--transition);
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
            transition: var(--transition);
        }

        .menu-item.active i,
        .menu-item:hover i {
            transform: scale(1.1);
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

        .admin-profile {
            display: flex;
            align-items: center;
            width: 100%;
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
            transition: var(--transition);
        }

        .admin-avatar:hover {
            transform: rotate(10deg);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
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

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: var(--transition);
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
            transition: var(--transition);
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
            transition: var(--transition);
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            width: 320px;
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
            transition: var(--transition);
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
        }

        .header-icon:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.2);
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
            transition: var(--transition);
        }

        .theme-toggle:hover {
            background: var(--primary-light);
            color: var(--primary-color);
        }

        .content {
            padding: 32px;
        }

        /* Dashboard Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 2rem;
            transition: var(--transition);
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1);
        }

        .bookings-icon {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(37, 99, 235, 0.1) 100%);
            color: var(--primary-color);
        }

        .revenue-icon {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%);
            color: var(--success-color);
        }

        .users-icon {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(245, 158, 11, 0.1) 100%);
            color: var(--warning-color);
        }

        .destinations-icon {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0.1) 100%);
            color: var(--error-color);
        }

        .stat-info h3 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-info p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            margin-top: 8px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .trend-up {
            color: var(--success-color);
        }

        .trend-down {
            color: var(--error-color);
        }

        /* Charts and Graphs */
        .charts-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        .chart-card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .chart-card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-actions {
            display: flex;
            gap: 12px;
        }

        .card-action {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
        }

        .card-action:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .chart-placeholder {
            height: 320px;
            background: var(--bg-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            flex-direction: column;
            transition: var(--transition);
        }

        .chart-placeholder i {
            font-size: 2.5rem;
            margin-bottom: 16px;
            opacity: 0.7;
        }

        /* Recent Activities */
        .activities-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            padding: 20px 0;
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .activity-item:hover {
            background: var(--bg-primary);
            border-radius: 12px;
            padding: 20px;
            margin: 0 -10px;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .booking-activity {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(37, 99, 235, 0.1) 100%);
            color: var(--primary-color);
        }

        .payment-activity {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0.1) 100%);
            color: var(--success-color);
        }

        .user-activity {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(245, 158, 11, 0.1) 100%);
            color: var(--warning-color);
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .activity-time {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .activity-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            align-self: center;
        }

        .completed-badge {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
        }

        .pending-badge {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning-color);
        }

        /* Recent Bookings Table */
        .table-card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--shadow);
            overflow: auto;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .table-card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.9rem;
            background: var(--bg-primary);
        }

        tbody tr {
            transition: var(--transition);
        }

        tbody tr:hover {
            background: var(--bg-primary);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-confirmed {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning-color);
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error-color);
        }

        .action-btn {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            margin-right: 8px;
        }

        .view-btn {
            background: rgba(37, 99, 235, 0.15);
            color: var(--primary-color);
        }

        .view-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .edit-btn {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
        }

        .edit-btn:hover {
            background: var(--success-color);
            color: white;
        }

        .delete-btn {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error-color);
        }

        .delete-btn:hover {
            background: var(--error-color);
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .charts-container {
                grid-template-columns: 1fr;
            }

            .search-input {
                width: 200px;
            }

            .search-input:focus {
                width: 240px;
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

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

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
        }

        @media (max-width: 576px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .card-actions {
                align-self: flex-end;
            }
        }
    </style>
</head>

<body>
    <
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
                <a href="./dashboard.php" class="menu-item active">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-calendar-event-line"></i>
                    <span>Bookings</span>
                    <span class="menu-badge">12</span>
                </a>
               
            </div>

            <div class="menu-section">
                <div class="menu-label">Content</div>
                <a href="./pages/manage-destinations.php" class="menu-item">
                    <i class="ri-map-2-line"></i>
                    <span>Destinations</span>
                </a>
                <a href="./pages/manage-packages.php" class="menu-item">
                    <i class="ri-briefcase-4-line"></i>
                    <span>Packages</span>
                </a>
                <a href="./pages/manage-gallery.php" class="menu-item">
                    <i class="ri-gallery-line"></i>
                    <span>Gallery</span>
                </a>
                <a href="./pages/manage-car-rentals.php" class="menu-item">
                    <i class="ri-car-line"></i>
                    <span>Car Rentals</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-label">Users</div>
                <!-- <a href="#" class="menu-item">
                    <i class="ri-user-line"></i>
                    <span>Customers</span>
                </a> -->
                <a href="#" class="menu-item">
                    <i class="ri-user-settings-line"></i>
                    <span>Admin Users</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-label">Settings</div>
                <a href="#" class="menu-item">
                    <i class="ri-settings-3-line"></i>
                    <span>Settings</span>
                </a>
                <a href="#" class="menu-item">
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
    <div class="main-content">
        <?php include './includes/header.php'; ?>

        <div class="content">
            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon bookings-icon">
                        <i class="ri-calendar-check-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>3</h3>
                        <p>Total packages</p>

                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon revenue-icon">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>3</h3>
                        <p>Total Vehicles</p>

                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon users-icon">
                        <i class="ri-user-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>8</h3>
                        <p>Registered Admins</p>

                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon destinations-icon">
                        <i class="ri-map-pin-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>12</h3>
                        <p>Destinations</p>
                    </div>
                </div>
            </div>



            <!-- Recent Activities -->
            <div class="charts-container">
                <div class="chart-card">
                    <div class="card-header">
                        <div class="card-title">Recent Activities</div>
                        <!-- <div class="card-actions">
                            <div class="card-action">
                                <i class="ri-more-2-fill"></i>
                            </div>
                        </div> -->
                    </div>
                    <ul class="activity-list">
                        <li class="activity-item">
                            <div class="activity-icon booking-activity">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">New booking for Kashmir Valley Explorer</div>
                                <div class="activity-time">10 minutes ago</div>
                            </div>
                            <span class="activity-badge completed-badge">Completed</span>
                        </li>

                        <li class="activity-item">
                            <div class="activity-icon payment-activity">
                                <i class="ri-bank-card-line"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Payment received from Rajesh Kumar</div>
                                <div class="activity-time">45 minutes ago</div>
                            </div>
                            <span class="activity-badge completed-badge">Completed</span>
                        </li>

                        <li class="activity-item">
                            <div class="activity-icon user-activity">
                                <i class="ri-user-add-line"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">New user registered: Priya Singh</div>
                                <div class="activity-time">2 hours ago</div>
                            </div>
                            <span class="activity-badge completed-badge">Completed</span>
                        </li>

                        <li class="activity-item">
                            <div class="activity-icon booking-activity">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Booking cancellation request</div>
                                <div class="activity-time">5 hours ago</div>
                            </div>
                            <span class="activity-badge pending-badge">Pending</span>
                        </li>
                    </ul>
                </div>

                <div class="chart-card">
                    <div class="card-header">
                        <div class="card-title">Upcoming Bookings</div>
                        <!-- <div class="card-actions">
                            <div class="card-action">
                                <i class="ri-more-2-fill"></i>
                            </div>
                        </div> -->
                    </div>
                    <ul class="activity-list">
                        <li class="activity-item">
                            <div class="activity-icon booking-activity">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Kashmir Valley Explorer</div>
                                <div class="activity-time">Tomorrow, 10:00 AM</div>
                            </div>
                        </li>

                        <li class="activity-item">
                            <div class="activity-icon booking-activity">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Ladakh Adventure</div>
                                <div class="activity-time">Aug 15, 9:00 AM</div>
                            </div>
                        </li>

                        <li class="activity-item">
                            <div class="activity-icon booking-activity">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Gulmarg Ski Package</div>
                                <div class="activity-time">Aug 18, 11:00 AM</div>
                            </div>
                        </li>

                        <li class="activity-item">
                            <div class="activity-icon booking-activity">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Pahalgam Family Tour</div>
                                <div class="activity-time">Aug 20, 10:30 AM</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>


        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dark/Light mode toggle
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = themeToggle.querySelector('i');

            themeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');

                if (document.body.classList.contains('dark-mode')) {
                    themeIcon.classList.remove('ri-moon-line');
                    themeIcon.classList.add('ri-sun-line');
                    localStorage.setItem('theme', 'dark');
                } else {
                    themeIcon.classList.remove('ri-sun-line');
                    themeIcon.classList.add('ri-moon-line');
                    localStorage.setItem('theme', 'light');
                }
            });

            // Check for saved theme preference
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark-mode');
                themeIcon.classList.remove('ri-moon-line');
                themeIcon.classList.add('ri-sun-line');
            }

            // Simulate loading data with animations
            setTimeout(() => {
                // Update stats with animation
                const stats = document.querySelectorAll('.stat-info h3');
                stats.forEach(stat => {
                    const originalText = stat.textContent;
                    stat.textContent = '0';

                    let counter = 0;
                    const target = originalText.replace(/\D/g, '');
                    const duration = 2000;
                    const increment = target / (duration / 16);

                    const updateCounter = () => {
                        if (counter < target) {
                            counter += increment;
                            stat.textContent = Math.ceil(counter).toLocaleString();
                            setTimeout(updateCounter, 16);
                        } else {
                            stat.textContent = originalText;
                        }
                    };

                    updateCounter();
                });
            }, 1000);

            // Add hover effects to cards
            const cards = document.querySelectorAll('.chart-card, .stat-card, .table-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-5px)';
                });

                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0)';
                });
            });

            // Notification click handler
            const notificationIcons = document.querySelectorAll('.header-icon');
            notificationIcons.forEach(icon => {
                icon.addEventListener('click', function() {
                    const badge = this.querySelector('.notification-badge');
                    if (badge) {
                        badge.style.display = 'none';
                    }
                });
            });

            // Menu item active state
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {

                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>

</html>