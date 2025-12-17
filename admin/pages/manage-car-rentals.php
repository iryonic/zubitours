<?php
session_start();
require_once '../includes/connection.php';


// Check if user is admin
// if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
//     header('Location: ../login.php');
//     exit();
// }

// Handle CRUD operations
$message = '';
$message_type = '';

// Add new car
if (isset($_POST['add_car'])) {
    $car_name = $_POST['car_name'];
    $car_type = $_POST['car_type'];
    $capacity = $_POST['capacity'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $price_per_day = $_POST['price_per_day'];
    $description = $_POST['description'];
    $badge = $_POST['badge'];
    
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/car/';
        $file_name = uniqid() . '_' . basename($_FILES['car_image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['car_image']['tmp_name'], $target_file)) {
            $image_path = 'cars/' . $file_name;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO car_rentals (car_name, car_type, capacity, transmission, fuel_type, price_per_day, description, image_path, badge) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisssdss", $car_name, $car_type, $capacity, $transmission, $fuel_type, $price_per_day, $description, $image_path, $badge);
    
    if ($stmt->execute()) {
        $message = "Car added successfully!";
        $message_type = "success";
    } else {
        $message = "Error adding car: " . $conn->error;
        $message_type = "error";
    }
}

// Update car
if (isset($_POST['update_car'])) {
    $id = $_POST['car_id'];
    $car_name = $_POST['car_name'];
    $car_type = $_POST['car_type'];
    $capacity = $_POST['capacity'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $price_per_day = $_POST['price_per_day'];
    $description = $_POST['description'];
    $badge = $_POST['badge'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Handle image upload if new image is provided
    $image_query = "";
    $params = [];
    $types = "ssisssdsi";
    
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/img/cars/';
        $file_name = uniqid() . '_' . basename($_FILES['car_image']['name']);
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['car_image']['tmp_name'], $target_file)) {
            $image_path = 'cars/' . $file_name;
            $image_query = ", image_path = ?";
            $types .= "s";
            $params[] = $image_path;
        }
    }
    
    $stmt = $conn->prepare("UPDATE car_rentals SET car_name = ?, car_type = ?, capacity = ?, transmission = ?, fuel_type = ?, price_per_day = ?, description = ?, badge = ?, is_available = ? $image_query WHERE id = ?");
    
    if ($image_query) {
        $stmt->bind_param($types, $car_name, $car_type, $capacity, $transmission, $fuel_type, $price_per_day, $description, $badge, $is_available, $image_path, $id);
    } else {
        $stmt->bind_param($types, $car_name, $car_type, $capacity, $transmission, $fuel_type, $price_per_day, $description, $badge, $is_available, $id);
    }
    
    if ($stmt->execute()) {
        $message = "Car updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating car: " . $conn->error;
        $message_type = "error";
    }
}

// Delete car
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // First, delete bookings for this car
    $conn->query("DELETE FROM car_bookings WHERE car_id = $id");
    
    // Then delete the car
    if ($conn->query("DELETE FROM car_rentals WHERE id = $id")) {
        $message = "Car deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting car: " . $conn->error;
        $message_type = "error";
    }
}

// Update booking status
if (isset($_POST['update_booking_status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];
    
    $stmt = $conn->prepare("UPDATE car_bookings SET status = ?, notes = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $notes, $booking_id);
    
    if ($stmt->execute()) {
        $message = "Booking status updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating booking: " . $conn->error;
        $message_type = "error";
    }
}

// Fetch all cars
$cars = $conn->query("SELECT * FROM car_rentals ORDER BY created_at DESC");

// Fetch all bookings
$bookings = $conn->query("
    SELECT cb.*, cr.car_name 
    FROM car_bookings cb 
    JOIN car_rentals cr ON cb.car_id = cr.id 
    ORDER BY cb.booking_date DESC
");

// Get stats
$total_cars = $conn->query("SELECT COUNT(*) as count FROM car_rentals")->fetch_assoc()['count'];
$available_cars = $conn->query("SELECT COUNT(*) as count FROM car_rentals WHERE is_available = 1")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM car_bookings")->fetch_assoc()['count'];
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM car_bookings WHERE status = 'pending'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Car Rentals - Zubi Tours Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <style>
        /* Reuse the same CSS variables and styles from adminpannel.php */
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

        /* Main Content Styles (same as adminpannel.php) */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: var(--transition);
        }

        .content {
            padding: 32px;
        }

        /* Header and sidebar styles are included from adminpannel.php */

        /* Custom styles for car rentals management */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .section-title {
            font-size: 1.7rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.2);
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background: #0da271;
        }

        .btn-warning {
            background: var(--warning-color);
            color: white;
        }

        .btn-danger {
            background: var(--error-color);
            color: white;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.875rem;
        }

        .tabs {
            display: flex;
            gap: 4px;
            background: var(--bg-secondary);
            padding: 4px;
            border-radius: 12px;
            margin-bottom: 24px;
            border: 1px solid var(--border-color);
        }

        .tab {
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }

        .tab.active {
            background: var(--primary-color);
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .message {
            padding: 16px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .message.error {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error-color);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .message i {
            font-size: 1.2rem;
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .car-card {
            display: flex;
            flex-direction: column;
        }

        .car-image {
            width: 100%;
            height: 200px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 16px;
            position: relative;
        }

        .car-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .car-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--primary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .car-info h3 {
            font-size: 1.2rem;
            margin-bottom: 8px;
        }

        .car-specs {
            display: flex;
            gap: 16px;
            margin-bottom: 12px;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .car-specs span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .car-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 16px;
        }

        .car-actions {
            display: flex;
            gap: 8px;
            margin-top: auto;
        }

        /* Table Styles */
        .table-container {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            overflow: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
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

        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning-color);
        }

        .status-confirmed {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error-color);
        }

        .status-completed {
            background: rgba(37, 99, 235, 0.15);
            color: var(--primary-color);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 32px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .close-modal:hover {
            color: var(--error-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input {
            width: auto;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.5rem;
        }

        .cars-icon {
            background: rgba(37, 99, 235, 0.15);
            color: var(--primary-color);
        }

        .available-icon {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
        }

        .bookings-icon {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning-color);
        }

        .pending-icon {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error-color);
        }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .stat-info p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .cards-grid {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                padding: 16px;
            }
            
            th, td {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar (same as adminpannel.php) -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="../../assets/img/zubilogo.jpg" alt="zubilogo" id="logo" style="height: 50px; width: auto; border-radius: 20px;">
            </div>
            <div class="sidebar-title">Zubi Tours</div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-label">Main</div>
                <a href="./dashboard.php" class="menu-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="ri-calendar-event-line"></i>
                    <span>Bookings</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-label">Content</div>
                <a href="./manage-destinations.php" class="menu-item">
                    <i class="ri-map-2-line"></i>
                    <span>Destinations</span>
                </a>
                <a href="./manage-packages.php" class="menu-item">
                    <i class="ri-briefcase-4-line"></i>
                    <span>Packages</span>
                </a>
                <a href="./manage-gallery.php" class="menu-item">
                    <i class="ri-gallery-line"></i>
                    <span>Gallery</span>
                </a>
                <a href="./manage-car-rentals.php" class="menu-item active">
                    <i class="ri-car-line"></i>
                    <span>Car Rentals</span>
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-label">Users</div>
                <a href="./manage-admins.php" class="menu-item">
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
                <a href="../logout.php" class="menu-item">
                    <i class="ri-logout-box-line"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <div class="admin-avatar">
                    <img src="../../assets/img/zubilogo.jpg" alt="zubilogo" style="height: 40px; width: auto; border-radius: 20px;">
                </div>
                <div class="admin-info">
                    <div class="admin-name"><?php echo $_SESSION['user_name'] ?? 'Admin'; ?></div>
                    <div class="admin-role"><?php echo $_SESSION['user_role'] ?? 'Admin'; ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
            <?php include '../includes/header.php'; ?>

        <div class="content">
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <i class="ri-<?php echo $message_type == 'success' ? 'check' : 'close'; ?>-circle-fill"></i>
                    <span><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <div class="section-header">
                <h1 class="section-title">Manage Car Rentals</h1>
                <button class="btn btn-primary" onclick="openAddCarModal()">
                    <i class="ri-add-line"></i> Add New Car
                </button>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon cars-icon">
                        <i class="ri-car-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_cars; ?></h3>
                        <p>Total Cars</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon available-icon">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $available_cars; ?></h3>
                        <p>Available Cars</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bookings-icon">
                        <i class="ri-calendar-check-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_bookings; ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon pending-icon">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $pending_bookings; ?></h3>
                        <p>Pending Bookings</p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" onclick="switchTab('cars')">Cars List</div>
                <div class="tab" onclick="switchTab('bookings')">Bookings</div>
            </div>

            <!-- Cars List Tab -->
            <div id="cars-tab" class="tab-content active">
                <div class="cards-grid">
                    <?php while ($car = $cars->fetch_assoc()): ?>
                        <div class="card car-card">
                            <div class="car-image">
                                <img src="../../assets/img/<?php echo $car['image_path'] ?: 'car1.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($car['car_name']); ?>"
                                     onerror="this.src='../../assets/img/car1.jpg'">
                                <?php if ($car['badge']): ?>
                                    <span class="car-badge"><?php echo $car['badge']; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="car-info">
                                <h3><?php echo htmlspecialchars($car['car_name']); ?></h3>
                                <div class="car-specs">
                                    <span><i class="ri-user-line"></i> <?php echo $car['capacity']; ?> Seater</span>
                                    <span><i class="ri-settings-3-line"></i> <?php echo ucfirst($car['transmission']); ?></span>
                                    <span><i class="ri-gas-station-line"></i> <?php echo ucfirst($car['fuel_type']); ?></span>
                                </div>
                                <div class="car-price">₹<?php echo number_format($car['price_per_day'], 2); ?> <span>/day</span></div>
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 16px;">
                                    <?php echo htmlspecialchars(substr($car['description'], 0, 100)) . '...'; ?>
                                </p>
                                <div class="car-actions">
                                    <button class="btn btn-primary btn-sm" onclick="editCar(<?php echo $car['id']; ?>)">
                                        <i class="ri-edit-line"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteCar(<?php echo $car['id']; ?>)">
                                        <i class="ri-delete-bin-line"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Bookings Tab -->
            <div id="bookings-tab" class="tab-content">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Car</th>
                                <th>Customer</th>
                                <th>Dates</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $bookings->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($booking['car_name']); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong><br>
                                        <small><?php echo $booking['customer_email']; ?></small>
                                    </td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?> - <br>
                                        <?php echo date('M d, Y', strtotime($booking['return_date'])); ?>
                                    </td>
                                    <td>₹<?php echo number_format($booking['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="viewBooking(<?php echo $booking['id']; ?>)">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="updateBookingStatus(<?php echo $booking['id']; ?>)">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Car Modal -->
    <div class="modal" id="car-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modal-title">Add New Car</h2>
            <form id="car-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="car_id" name="car_id">
                <input type="hidden" name="add_car" id="form-action">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="car_name">Car Name *</label>
                        <input type="text" id="car_name" name="car_name" required>
                    </div>
                    <div class="form-group">
                        <label for="car_type">Car Type *</label>
                        <select id="car_type" name="car_type" required>
                            <option value="">Select Type</option>
                            <option value="suv">SUV</option>
                            <option value="sedan">Sedan</option>
                            <option value="luxury">Luxury</option>
                            <option value="economy">Economy</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="capacity">Capacity (Seaters) *</label>
                        <input type="number" id="capacity" name="capacity" min="1" max="50" required>
                    </div>
                    <div class="form-group">
                        <label for="transmission">Transmission *</label>
                        <select id="transmission" name="transmission" required>
                            <option value="manual">Manual</option>
                            <option value="automatic">Automatic</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fuel_type">Fuel Type *</label>
                        <select id="fuel_type" name="fuel_type" required>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="electric">Electric</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price_per_day">Price per Day (₹) *</label>
                        <input type="number" id="price_per_day" name="price_per_day" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="badge">Badge (Optional)</label>
                        <select id="badge" name="badge">
                            <option value="">No Badge</option>
                            <option value="Popular">Popular</option>
                            <option value="Group">Group</option>
                            <option value="Luxury">Luxury</option>
                            <option value="Economy">Economy</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="car_image">Car Image</label>
                        <input type="file" id="car_image" name="car_image" accept="image/*">
                        <small style="color: var(--text-secondary);">Leave empty to keep current image</small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_available" name="is_available" value="1" checked>
                        <label for="is_available">Available for booking</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="ri-save-line"></i> Save Car
                </button>
            </form>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div class="modal" id="booking-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeBookingModal()">&times;</span>
            <h2>Booking Details</h2>
            <div id="booking-details"></div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal" id="status-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeStatusModal()">&times;</span>
            <h2>Update Booking Status</h2>
            <form id="status-form" method="POST">
                <input type="hidden" id="booking_id" name="booking_id">
                <input type="hidden" name="update_booking_status" value="1">
                
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Notes (Optional)</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Add any notes about this booking..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="ri-check-line"></i> Update Status
                </button>
            </form>
        </div>
    </div>

    <script>
        // Tab switching
        function switchTab(tabName) {
            // Update tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Activate selected tab
            document.querySelector(`.tab:nth-child(${tabName === 'cars' ? 1 : 2})`).classList.add('active');
            document.getElementById(`${tabName}-tab`).classList.add('active');
        }

        // Modal functions
        function openAddCarModal() {
            document.getElementById('modal-title').textContent = 'Add New Car';
            document.getElementById('form-action').value = 'add_car';
            document.getElementById('car-form').reset();
            document.getElementById('car-form').action = '';
            document.getElementById('car_id').value = '';
            document.getElementById('car-modal').classList.add('active');
        }

        function editCar(carId) {
            // Fetch car details via AJAX
            fetch(`../logic/get_car.php?id=${carId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modal-title').textContent = 'Edit Car';
                    document.getElementById('form-action').value = 'update_car';
                    document.getElementById('car_id').value = data.id;
                    document.getElementById('car_name').value = data.car_name;
                    document.getElementById('car_type').value = data.car_type;
                    document.getElementById('capacity').value = data.capacity;
                    document.getElementById('transmission').value = data.transmission;
                    document.getElementById('fuel_type').value = data.fuel_type;
                    document.getElementById('price_per_day').value = data.price_per_day;
                    document.getElementById('description').value = data.description || '';
                    document.getElementById('badge').value = data.badge || '';
                    document.getElementById('is_available').checked = data.is_available == 1;
                    
                    document.getElementById('car-modal').classList.add('active');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading car details');
                });
        }

        function deleteCar(carId) {
            if (confirm('Are you sure you want to delete this car? All related bookings will also be deleted.')) {
                window.location.href = `?delete=${carId}`;
            }
        }

        function viewBooking(bookingId) {
            fetch(`../logic/get_booking.php?id=${bookingId}`)
                .then(response => response.json())
                .then(data => {
                    const details = `
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Booking Information</h3>
                            <p><strong>Booking ID:</strong> #${String(data.id).padStart(6, '0')}</p>
                            <p><strong>Car:</strong> ${data.car_name}</p>
                            <p><strong>Customer:</strong> ${data.customer_name}</p>
                            <p><strong>Email:</strong> ${data.customer_email}</p>
                            <p><strong>Phone:</strong> ${data.customer_phone}</p>
                            <p><strong>Driving License:</strong> ${data.customer_driving_license || 'N/A'}</p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Trip Details</h3>
                            <p><strong>Pickup Location:</strong> ${data.pickup_location}</p>
                            <p><strong>Pickup Date:</strong> ${new Date(data.pickup_date).toLocaleDateString('en-US', { 
                                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
                            })}</p>
                            <p><strong>Return Date:</strong> ${new Date(data.return_date).toLocaleDateString('en-US', { 
                                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
                            })}</p>
                            <p><strong>Total Days:</strong> ${data.total_days}</p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Payment Details</h3>
                            <p><strong>Price per Day:</strong> ₹${parseFloat(data.total_amount / data.total_days).toFixed(2)}</p>
                            <p><strong>Total Amount:</strong> ₹${parseFloat(data.total_amount).toFixed(2)}</p>
                            <p><strong>Status:</strong> <span class="status-badge status-${data.status}">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span></p>
                        </div>
                        
                        ${data.notes ? `
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Notes</h3>
                            <p>${data.notes}</p>
                        </div>
                        ` : ''}
                        
                        <div style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 24px;">
                            <p><strong>Booking Date:</strong> ${new Date(data.booking_date).toLocaleString()}</p>
                        </div>
                    `;
                    
                    document.getElementById('booking-details').innerHTML = details;
                    document.getElementById('booking-modal').classList.add('active');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading booking details');
                });
        }

        function updateBookingStatus(bookingId) {
            document.getElementById('booking_id').value = bookingId;
            document.getElementById('status-modal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('car-modal').classList.remove('active');
        }

        function closeBookingModal() {
            document.getElementById('booking-modal').classList.remove('active');
        }

        function closeStatusModal() {
            document.getElementById('status-modal').classList.remove('active');
        }

        // Close modals when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });

        // Form validation
        document.getElementById('car-form').addEventListener('submit', function(e) {
            const price = document.getElementById('price_per_day').value;
            if (price < 0) {
                e.preventDefault();
                alert('Price cannot be negative');
                return false;
            }
            
            const capacity = document.getElementById('capacity').value;
            if (capacity < 1 || capacity > 50) {
                e.preventDefault();
                alert('Capacity must be between 1 and 50');
                return false;
            }
            
            return true;
        });

        // Initialize date pickers
        document.addEventListener('DOMContentLoaded', function() {
            // Set min dates for date inputs
            const today = new Date().toISOString().split('T')[0];
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                input.min = today;
            });
        });
    </script>
</body>
</html>