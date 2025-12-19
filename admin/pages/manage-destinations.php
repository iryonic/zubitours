<?php
session_start();
require_once '../includes/connection.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}

// Handle CRUD operations
$message = '';
$message_type = '';

// Flash messages (Post/Redirect/Get) — show once then clear
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// Add new destination
if (isset($_POST['add_destination'])) {
    $destination_name = $_POST['destination_name'];
    $region = $_POST['region'];
    $destination_type = $_POST['destination_type'];
    $best_seasons = json_encode($_POST['best_seasons'] ?? []);
    $location = $_POST['location'];
    $short_description = $_POST['short_description'];
    $badge = $_POST['badge'];
    $rating = $_POST['rating'];
    $reviews_count = $_POST['reviews_count'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $stmt = $conn->prepare("INSERT INTO destinations (destination_name, region, destination_type, best_seasons, location, short_description, badge, rating, reviews_count, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssdsii", $destination_name, $region, $destination_type, $best_seasons, $location, $short_description, $badge, $rating, $reviews_count, $is_featured, $is_active);
    
    if ($stmt->execute()) {
        $destination_id = $conn->insert_id;
        
        // Handle multiple image uploads
        if (isset($_FILES['destination_images']) && !empty($_FILES['destination_images']['name'][0])) {
            $upload_dir = '../upload/destinations/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            foreach ($_FILES['destination_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['destination_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = uniqid() . '_' . basename($_FILES['destination_images']['name'][$key]);
                    $target_file = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $image_path = 'destinations/' . $file_name;
                        $is_primary = ($key === 0) ? 1 : 0;
                        
                        $img_stmt = $conn->prepare("INSERT INTO destination_images (destination_id, image_path, is_primary) VALUES (?, ?, ?)");
                        $img_stmt->bind_param("iss", $destination_id, $image_path, $is_primary);
                        $img_stmt->execute();
                    }
                }
            }
        }
        
        $_SESSION['flash_message'] = "Destination added successfully!";
        $_SESSION['flash_type'] = "success";
        header('Location: manage-destinations.php');
        exit();
    } else {
        $_SESSION['flash_message'] = "Error adding destination: " . $conn->error;
        $_SESSION['flash_type'] = "error";
        header('Location: manage-destinations.php');
        exit();
    }
}

// Update destination
if (isset($_POST['update_destination'])) {
    $id = $_POST['destination_id'];
    $destination_name = $_POST['destination_name'];
    $region = $_POST['region'];
    $destination_type = $_POST['destination_type'];
    $best_seasons = json_encode($_POST['best_seasons'] ?? []);
    $location = $_POST['location'];
    $short_description = $_POST['short_description'];
    $badge = $_POST['badge'];
    $rating = $_POST['rating'];
    $reviews_count = $_POST['reviews_count'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE destinations SET destination_name = ?, region = ?, destination_type = ?, best_seasons = ?, location = ?, short_description = ?, badge = ?, rating = ?, reviews_count = ?, is_featured = ?, is_active = ? WHERE id = ?");
    $stmt->bind_param("sssssssdsiii", $destination_name, $region, $destination_type, $best_seasons, $location, $short_description, $badge, $rating, $reviews_count, $is_featured, $is_active, $id);
    
    if ($stmt->execute()) {
        $destination_id = $id;
        if (isset($_FILES['destination_images']) && !empty($_FILES['destination_images']['name'][0])) {
            $upload_dir = '../upload/destinations/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            foreach ($_FILES['destination_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['destination_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = uniqid() . '_' . basename($_FILES['destination_images']['name'][$key]);
                    $target_file = $upload_dir . $file_name;
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $image_path = 'destinations/' . $file_name;
                        $is_primary = ($key === 0) ? 1 : 0;
                        $img_stmt = $conn->prepare("INSERT INTO destination_images (destination_id, image_path, is_primary) VALUES (?, ?, ?)");
                        $img_stmt->bind_param("iss", $destination_id, $image_path, $is_primary);
                        $img_stmt->execute();
                    }
                }   
            }
        }

        $_SESSION['flash_message'] = "Destination updated successfully!";
        $_SESSION['flash_type'] = "success";
        header('Location: manage-destinations.php');
        exit();
    } else {
        $_SESSION['flash_message'] = "Error updating destination: " . $conn->error;
        $_SESSION['flash_type'] = "error";
        header('Location: manage-destinations.php');
        exit();
    }
}

// Delete destination
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Delete related images first
    $images = $conn->query("SELECT image_path FROM destination_images WHERE destination_id = $id");
    while ($image = $images->fetch_assoc()) {
        $file_path = '../upload/' . $image['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Delete image records
    $conn->query("DELETE FROM destination_images WHERE destination_id = $id");
    
    if ($conn->query("DELETE FROM destinations WHERE id = $id")) {
        $_SESSION['flash_message'] = "Destination deleted successfully!";
        $_SESSION['flash_type'] = "success";
        header('Location: manage-destinations.php');
        exit();
    } else {
        $_SESSION['flash_message'] = "Error deleting destination: " . $conn->error;
        $_SESSION['flash_type'] = "error";
        header('Location: manage-destinations.php');
        exit();
    }
}

// Delete image
if (isset($_GET['delete_image'])) {
    $image_id = $_GET['delete_image'];
    
    $image_result = $conn->query("SELECT image_path FROM destination_images WHERE id = $image_id");
    if ($image_result->num_rows > 0) {
        $image = $image_result->fetch_assoc();
        $file_path = '../upload/' . $image['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    if ($conn->query("DELETE FROM destination_images WHERE id = $image_id")) {
        $_SESSION['flash_message'] = "Image deleted successfully!";
        $_SESSION['flash_type'] = "success";
        header('Location: manage-destinations.php');
        exit();
    } else {
        $_SESSION['flash_message'] = "Error deleting image: " . $conn->error;
        $_SESSION['flash_type'] = "error";
        header('Location: manage-destinations.php');
        exit();
    }
}

// Upload images directly from Images modal (posts destination_id + destination_images[])
if (isset($_POST['destination_id']) && isset($_FILES['destination_images']) && !empty($_FILES['destination_images']['name'][0])) {
    $destination_id = intval($_POST['destination_id']);
    $upload_dir = '../upload/destinations/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $uploadedAny = false;
    foreach ($_FILES['destination_images']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['destination_images']['error'][$key] === UPLOAD_ERR_OK) {
            $file_name = uniqid() . '_' . basename($_FILES['destination_images']['name'][$key]);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($tmp_name, $target_file)) {
                $image_path = 'destinations/' . $file_name;
                $is_primary = 0; // do not change primary via this flow
                $img_stmt = $conn->prepare("INSERT INTO destination_images (destination_id, image_path, is_primary) VALUES (?, ?, ?)");
                $img_stmt->bind_param("iss", $destination_id, $image_path, $is_primary);
                $img_stmt->execute();
                $uploadedAny = true;
            }
        }
    }
    if ($uploadedAny) {
        $_SESSION['flash_message'] = "Images uploaded successfully!";
        $_SESSION['flash_type'] = "success";
        header('Location: manage-destinations.php');
        exit();
    } else {
        $_SESSION['flash_message'] = "No images uploaded or there was an upload error.";
        $_SESSION['flash_type'] = "error";
        header('Location: manage-destinations.php');
        exit();
    }
}

// Set primary image
if (isset($_GET['set_primary'])) {
    $image_id = $_GET['set_primary'];
    
    $img_result = $conn->query("SELECT destination_id FROM destination_images WHERE id = $image_id");
    if ($img_result->num_rows > 0) {
        $img = $img_result->fetch_assoc();
        $destination_id = $img['destination_id'];
        
        $conn->query("UPDATE destination_images SET is_primary = 0 WHERE destination_id = $destination_id");
        
        if ($conn->query("UPDATE destination_images SET is_primary = 1 WHERE id = $image_id")) {
            $_SESSION['flash_message'] = "Primary image updated successfully!";
            $_SESSION['flash_type'] = "success";
            header('Location: manage-destinations.php');
            exit();
        }
    }
}

// Bulk actions
if (isset($_POST['bulk_action'])) {
    $action = $_POST['bulk_action'];
    $selected_destinations = $_POST['selected_destinations'] ?? [];
    
    if (!empty($selected_destinations)) {
        $ids = implode(',', array_map('intval', $selected_destinations));
        
        switch ($action) {
            case 'activate':
                $conn->query("UPDATE destinations SET is_active = 1 WHERE id IN ($ids)");
                $flash = "Selected destinations activated!";
                break;
            case 'deactivate':
                $conn->query("UPDATE destinations SET is_active = 0 WHERE id IN ($ids)");
                $flash = "Selected destinations deactivated!";
                break;
            case 'feature':
                $conn->query("UPDATE destinations SET is_featured = 1 WHERE id IN ($ids)");
                $flash = "Selected destinations marked as featured!";
                break;
            case 'unfeature':
                $conn->query("UPDATE destinations SET is_featured = 0 WHERE id IN ($ids)");
                $flash = "Selected destinations unfeatured!";
                break;
            case 'delete':
                // Delete images first
                $images = $conn->query("SELECT image_path FROM destination_images WHERE destination_id IN ($ids)");
                while ($image = $images->fetch_assoc()) {
                    $file_path = '../upload/' . $image['image_path'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
                $conn->query("DELETE FROM destination_images WHERE destination_id IN ($ids)");
                $conn->query("DELETE FROM destinations WHERE id IN ($ids)");
                $flash = "Selected destinations deleted!";
                break;
        }
        $_SESSION['flash_message'] = $flash ?? 'Bulk action completed.';
        $_SESSION['flash_type'] = 'success';
        header('Location: manage-destinations.php');
        exit();
    }
}

// Fetch all destinations with their primary images
$destinations = $conn->query("
    SELECT d.*, di.image_path 
    FROM destinations d 
    LEFT JOIN destination_images di ON d.id = di.destination_id AND di.is_primary = 1 
    ORDER BY d.created_at DESC
");

// Get stats
$total_destinations = $conn->query("SELECT COUNT(*) as count FROM destinations")->fetch_assoc()['count'];
$active_destinations = $conn->query("SELECT COUNT(*) as count FROM destinations WHERE is_active = 1")->fetch_assoc()['count'];
$featured_destinations = $conn->query("SELECT COUNT(*) as count FROM destinations WHERE is_featured = 1")->fetch_assoc()['count'];
$kashmir_destinations = $conn->query("SELECT COUNT(*) as count FROM destinations WHERE region = 'kashmir'")->fetch_assoc()['count'];
$ladakh_destinations = $conn->query("SELECT COUNT(*) as count FROM destinations WHERE region = 'ladakh'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Destinations - Zubi Tours Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
     <link rel="stylesheet" href="../assets/admin.css">
     <style>
        .image-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .image-preview-item {
            position: relative;
            width: 100%;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid var(--border-color);
        }
        
        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .image-preview-item:hover .image-overlay {
            opacity: 1;
        }
        
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .tab.active {
            background: var(--primary-color);
            color: white;
        }
        
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 8px;
            margin-right: 5px;
            display: inline-block;
            background: var(--bg-secondary);
        }
        
        .tabs {
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .season-checkboxes {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        
        .season-checkbox {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .season-checkbox input[type="checkbox"] {
            width: auto;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-size: 14px;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: 16px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
            color: var(--text-secondary);
        }
        
        .close-modal:hover {
            color: var(--text-primary);
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .btn-danger {
            background: var(--error-color);
            color: white;
        }
        
        .btn-danger:hover {
            background: #dc2626;
        }
        
        .btn-warning {
            background: var(--warning-color);
            color: white;
        }
        
        .btn-warning:hover {
            background: #d97706;
        }
        
        .table-actions {
            display: flex;
            gap: 8px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-active {
            background: rgba(34, 197, 94, 0.15);
            color: #16a34a;
        }
        
        .status-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
        }
        
        .status-featured {
            background: rgba(245, 158, 11, 0.15);
            color: #d97706;
        }
        
        .message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
        }
        
        .message.success {
            background: rgba(34, 197, 94, 0.15);
            color: #16a34a;
            border-left: 4px solid #16a34a;
        }
        
        .message.error {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }
        
        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .quick-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .quick-action-btn {
            padding: 10px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        
        .quick-action-btn:hover {
            border-color: var(--primary-color);
            background: rgba(59, 130, 246, 0.05);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--bg-secondary);
            padding: 20px;
            border-radius: 12px;
            display: flex;
            gap: 15px;
            align-items: center;
            border: 1px solid var(--border-color);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        
        .destinations-icon {
            background: rgba(59, 130, 246, 0.15);
            color: var(--primary-color);
        }
        
        .kashmir-icon {
            background: rgba(147, 51, 234, 0.15);
            color: #9333ea;
        }
        
        .ladakh-icon {
            background: rgba(249, 115, 22, 0.15);
            color: #f97316;
        }
        
        .featured-icon {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }
        
        .stat-info h3 {
            font-size: 24px;
            margin: 0 0 5px 0;
            color: var(--text-primary);
        }
        
        .stat-info p {
            margin: 0 0 5px 0;
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        .stat-trend {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }
        
        .table-container {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border-color);
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table thead {
            background: var(--bg-primary);
        }
        
        .data-table th {
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 2px solid var(--border-color);
        }
        
        .data-table td {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .data-table tr:hover {
            background: rgba(59, 130, 246, 0.02);
        }
        
        .region-kashmir {
            color: #9333ea;
        }
        
        .region-ladakh {
            color: #f97316;
        }
        
        .region-jammu {
            color: #10b981;
        }
     </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

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
                <h1 class="section-title">Manage Destinations</h1>
                <div>
                    <button class="btn btn-primary" onclick="openAddDestinationModal()">
                        <i class="ri-add-line"></i> Add New Destination
                    </button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <button class="quick-action-btn" onclick="filterDestinations('active')">
                    <i class="ri-checkbox-circle-line"></i> Active
                </button>
                <button class="quick-action-btn" onclick="filterDestinations('featured')">
                    <i class="ri-star-line"></i> Featured
                </button>
                <button class="quick-action-btn" onclick="filterDestinations('kashmir')">
                    <i class="ri-map-pin-line"></i> Kashmir
                </button>
                <button class="quick-action-btn" onclick="filterDestinations('ladakh')">
                    <i class="ri-map-pin-line"></i> Ladakh
                </button>
                <button class="quick-action-btn" onclick="filterDestinations('lake')">
                    <i class="ri-water-flash-line"></i> Lakes
                </button>
                <button class="quick-action-btn" onclick="filterDestinations('adventure')">
                    <i class="ri-landscape-line"></i> Adventure
                </button>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon destinations-icon">
                        <i class="ri-map-pin-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_destinations; ?></h3>
                        <p>Total Destinations</p>
                        <div class="stat-trend">
                            <span><?php echo $active_destinations; ?> active</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon kashmir-icon">
                        <i class="ri-snowflake-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $kashmir_destinations; ?></h3>
                        <p>Kashmir Destinations</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon ladakh-icon">
                        <i class="ri-sun-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $ladakh_destinations; ?></h3>
                        <p>Ladakh Destinations</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon featured-icon">
                        <i class="ri-star-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $featured_destinations; ?></h3>
                        <p>Featured Destinations</p>
                    </div>
                </div>
            </div>

            <!-- Destinations Table -->
            <div class="table-container">
                <!-- Search -->
                <div style="margin-bottom: 20px;">
                    <div style="position: relative;">
                        <i class="ri-search-line" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-secondary);"></i>
                        <input type="text" id="destination-search" placeholder="Search destinations..." style="width: 100%; padding: 12px 16px 12px 48px; border: 1px solid var(--border-color); border-radius: 12px; background: var(--bg-secondary); color: var(--text-primary);" onkeyup="searchDestinations()">
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th width="300">Destination Details</th>
                            <th>Region</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="destinations-table-body">
                        <?php 
                        $counter = 1;
                        while ($destination = $destinations->fetch_assoc()): 
                            $seasons = json_decode($destination['best_seasons'], true) ?: [];
                            $seasonBadges = array_map(function($season) {
                                $icons = [
                                    'spring' => '🌼',
                                    'summer' => '☀️',
                                    'autumn' => '🍂',
                                    'winter' => '❄️'
                                ];
                                return '<span style="font-size: 0.8rem; margin-right: 2px;">' . ($icons[$season] ?? $season) . '</span>';
                            }, $seasons);
                        ?>
                            <tr class="destination-row" 
                                data-region="<?php echo $destination['region']; ?>"
                                data-type="<?php echo $destination['destination_type']; ?>"
                                data-status="<?php echo $destination['is_active'] ? 'active' : 'inactive'; ?>"
                                data-featured="<?php echo $destination['is_featured'] ? 'featured' : 'not-featured'; ?>">
                                <td><?php echo $counter; ?></td>
                                <td class="destination-name-cell">
                                    <div style="display: flex; gap: 15px; align-items: flex-start;">
                                        <div style="width: 80px; height: 80px; border-radius: 12px; overflow: hidden; flex-shrink: 0;">
                                            <img src="<?php echo !empty($destination['image_path']) ? '../upload/'. $destination['image_path'] : '../../assets/img/bg1.jpg'; ?>" 
                                                 alt="<?php echo htmlspecialchars($destination['destination_name']); ?>"
                                                 style="width: 100%; height: 100%; object-fit: cover;"
                                                 onerror="this.src='../../assets/img/bg1.jpg'">
                                        </div>
                                        <div style="flex: 1;">
                                            <h4 style="margin: 0 0 5px 0;"><?php echo htmlspecialchars($destination['destination_name']); ?></h4>
                                            <p style="margin: 0 0 8px 0; font-size: 0.9rem; color: var(--text-secondary);">
                                                <?php echo htmlspecialchars(substr($destination['short_description'], 0, 100)); ?>...
                                            </p>
                                            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                                <?php if ($destination['badge']): ?>
                                                    <span class="status-badge" style="background: rgba(37, 99, 235, 0.15); color: var(--primary-color); font-size: 0.7rem; padding: 3px 8px;">
                                                        <?php echo $destination['badge']; ?>
                                                    </span>
                                                <?php endif; ?>
                                                <div style="display: flex; align-items: center; gap: 5px; font-size: 0.8rem; color: var(--text-secondary);">
                                                    <?php echo implode('', $seasonBadges); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="region-<?php echo $destination['region']; ?>">
                                        <?php echo ucfirst($destination['region']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="type-<?php echo $destination['destination_type']; ?>" style="font-weight: 600;">
                                        <?php echo ucfirst($destination['destination_type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight: 500;">
                                        <i class="ri-map-pin-line" style="margin-right: 5px;"></i>
                                        <?php echo htmlspecialchars($destination['location']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <i class="ri-star-fill" style="color: #f59e0b;"></i>
                                        <span style="font-weight: 600;"><?php echo number_format($destination['rating'], 1); ?></span>
                                        <span style="font-size: 0.8rem; color: var(--text-secondary);">
                                            (<?php echo $destination['reviews_count']; ?>)
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($destination['is_active']): ?>
                                        <span class="status-badge status-active">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive">Inactive</span>
                                    <?php endif; ?>
                                    
                                    <?php if ($destination['is_featured']): ?>
                                        <br><span class="status-badge status-featured" style="margin-top: 5px;">Featured</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button class="btn btn-primary btn-sm" onclick="editDestination(<?php echo $destination['id']; ?>)">
                                            <i class="ri-edit-line"></i> Edit
                                        </button>
                                        <button class="btn btn-warning btn-sm" onclick="manageDestinationImages(<?php echo $destination['id']; ?>)">
                                            <i class="ri-image-line"></i> Images
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteDestination(<?php echo $destination['id']; ?>)">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                        $counter++;
                        endwhile; 
                        ?>
                    </tbody>
                </table>
                
                <?php if ($destinations->num_rows === 0): ?>
                    <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                        <i class="ri-inbox-line" style="font-size: 3rem; margin-bottom: 15px;"></i>
                        <h3>No destinations found</h3>
                        <p>Click "Add New Destination" to create your first destination.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add/Edit Destination Modal -->
    <div class="modal" id="destination-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modal-title">Add New Destination</h2>
            <form id="destination-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="destination_id" name="destination_id">
                <input type="hidden" id="form_action" name="form_action" value="add_destination">
                
                <div class="tabs" style="margin-bottom: 20px;">
                    <div class="tab active" data-tab="basic" onclick="switchFormTab('basic')">Basic Info</div>
                    <div class="tab" data-tab="media" onclick="switchFormTab('media')">Media</div>
                </div>
                
                <!-- Basic Info Tab -->
                <div id="basic-tab" class="tab-content active">
                    <div class="form-group">
                        <label for="destination_name">Destination Name *</label>
                        <input type="text" id="destination_name" name="destination_name" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="region">Region *</label>
                            <select id="region" name="region" required>
                                <option value="">Select Region</option>
                                <option value="kashmir">Kashmir</option>
                                <option value="ladakh">Ladakh</option>
                                <option value="jammu">Jammu</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="destination_type">Destination Type *</label>
                            <select id="destination_type" name="destination_type" required>
                                <option value="">Select Type</option>
                                <option value="lake">Lake</option>
                                <option value="valley">Valley</option>
                                <option value="mountain">Mountain</option>
                                <option value="monastery">Monastery</option>
                                <option value="hill">Hill Station</option>
                                <option value="desert">Desert</option>
                                <option value="cultural">Cultural</option>
                                <option value="adventure">Adventure</option>
                                <option value="scenic">Scenic</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Best Seasons *</label>
                        <div class="season-checkboxes">
                            <label class="season-checkbox">
                                <input type="checkbox" name="best_seasons[]" value="spring"> Spring
                            </label>
                            <label class="season-checkbox">
                                <input type="checkbox" name="best_seasons[]" value="summer"> Summer
                            </label>
                            <label class="season-checkbox">
                                <input type="checkbox" name="best_seasons[]" value="autumn"> Autumn
                            </label>
                            <label class="season-checkbox">
                                <input type="checkbox" name="best_seasons[]" value="winter"> Winter
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="location">Location *</label>
                        <input type="text" id="location" name="location" required placeholder="e.g., Srinagar, Baramulla">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="rating">Rating (0-5)</label>
                            <input type="number" id="rating" name="rating" step="0.1" min="0" max="5" value="4.5">
                        </div>
                        <div class="form-group">
                            <label for="reviews_count">Reviews Count</label>
                            <input type="number" id="reviews_count" name="reviews_count" min="0" value="100">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="badge">Badge (Optional)</label>
                        <select id="badge" name="badge">
                            <option value="">No Badge</option>
                            <option value="Popular">Popular</option>
                            <option value="Adventure">Adventure</option>
                            <option value="Cultural">Cultural</option>
                            <option value="Scenic">Scenic</option>
                            <option value="Luxury">Luxury</option>
                            <option value="Family">Family</option>
                            <option value="Romantic">Romantic</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="short_description">Short Description *</label>
                        <textarea id="short_description" name="short_description" rows="3" required placeholder="Brief description for cards and listings..."></textarea>
                    </div>
                </div>
                
                <!-- Media Tab -->
                <div id="media-tab" class="tab-content">
                    <div class="form-group">
                        <label for="destination_images">Destination Images</label>
                        <input type="file" id="destination_images" name="destination_images[]" accept="image/*" multiple>
                        <small style="color: var(--text-secondary);">Select multiple images (first image will be primary)</small>
                        <div id="images-preview" class="image-preview-grid"></div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1">
                                <label for="is_featured">Featured Destination</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="ri-save-line"></i> Save Destination
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Manage Images Modal -->
    <div class="modal" id="images-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeImagesModal()">&times;</span>
            <h2>Manage Destination Images</h2>
            <div id="images-list" style="margin: 20px 0;"></div>
            <form id="upload-images-form" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
                <input type="hidden" id="images_destination_id" name="destination_id">
                <div class="form-group">
                    <label>Add More Images</label>
                    <input type="file" name="destination_images[]" accept="image/*" multiple>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-upload-line"></i> Upload Images
                </button>
            </form>
        </div>
    </div>

    <script>
        // Global variables
        let currentDestinationId = null;
        
        // Tab switching
        function switchFormTab(tabName) {
            // Remove active from all tabs/contents
            document.querySelectorAll('#destination-modal .tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('#destination-modal .tab-content').forEach(content => content.classList.remove('active'));

            // Find the tab element by data attribute (more robust than relying on index)
            const tabEl = Array.from(document.querySelectorAll('#destination-modal .tab')).find(t => t.getAttribute('data-tab') === tabName);
            if (tabEl) tabEl.classList.add('active');

            const contentEl = document.getElementById(`${tabName}-tab`);
            if (contentEl) contentEl.classList.add('active');
        }
        
        // Modal functions
        function openAddDestinationModal() {
            document.getElementById('modal-title').textContent = 'Add New Destination';
            document.getElementById('form_action').name = 'add_destination';
            document.getElementById('form_action').value = 'add_destination';
            
            document.getElementById('destination-form').reset();
            document.getElementById('destination_id').value = '';
            
            switchFormTab('basic');
            
            // Clear image preview
            document.getElementById('images-preview').innerHTML = '';
            
            // Uncheck all season checkboxes
            document.querySelectorAll('input[name="best_seasons[]"]').forEach(cb => {
                cb.checked = false;
            });
            
            // Uncheck featured and active checkboxes
            document.getElementById('is_featured').checked = false;
            document.getElementById('is_active').checked = true;
            
            document.getElementById('destination-modal').classList.add('active');
        }
        
        // Image Preview
        document.getElementById('destination_images').addEventListener('change', function(e) {
            const preview = document.getElementById('images-preview');
            preview.innerHTML = '';
            
            Array.from(e.target.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('div');
                    img.className = 'image-preview-item';
                    img.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;">
                            <div style="color: white; text-align: center;">Image ${index + 1}</div>
                        </div>
                    `;
                    img.onmouseover = function() {
                        this.children[1].style.opacity = '1';
                    };
                    img.onmouseout = function() {
                        this.children[1].style.opacity = '0';
                    };
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        });

        // Edit Destination
        function editDestination(destinationId) {
            fetch(`../logic/get_destination.php?id=${destinationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    document.getElementById('modal-title').textContent = 'Edit Destination';
                    document.getElementById('form_action').name = 'update_destination';
                    document.getElementById('form_action').value = 'update_destination';
                    
                    document.getElementById('destination_id').value = data.id;
                    document.getElementById('destination_name').value = data.destination_name;
                    document.getElementById('region').value = data.region;
                    document.getElementById('destination_type').value = data.destination_type;
                    document.getElementById('location').value = data.location;
                    document.getElementById('short_description').value = data.short_description;
                    document.getElementById('badge').value = data.badge || '';
                    document.getElementById('rating').value = data.rating || '4.5';
                    document.getElementById('reviews_count').value = data.reviews_count || '100';
                    document.getElementById('is_featured').checked = data.is_featured == 1;
                    document.getElementById('is_active').checked = data.is_active == 1;
                    
                    // Set seasons
                    document.querySelectorAll('input[name="best_seasons[]"]').forEach(cb => {
                        cb.checked = data.best_seasons?.includes(cb.value) || false;
                    });
                    
                    switchFormTab('basic');
                    
                    // Clear current image preview
                    document.getElementById('images-preview').innerHTML = '';
                    
                    document.getElementById('destination-modal').classList.add('active');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading destination details');
                });
        }
        
        // Delete Destination
        function deleteDestination(destinationId) {
            if (confirm('Are you sure you want to delete this destination? All related images will also be deleted.')) {
                window.location.href = `?delete=${destinationId}`;
            }
        }
        
        // Manage Images
        function manageDestinationImages(destinationId) {
            document.getElementById('images_destination_id').value = destinationId;
            currentDestinationId = destinationId;
            loadDestinationImages(destinationId);
            document.getElementById('images-modal').classList.add('active');
        }
        
        function loadDestinationImages(destinationId) {
            fetch(`../logic/get_destination_images.php?id=${destinationId}`)
                .then(response => response.json())
                .then(images => {
                    let html = '<div class="image-preview-grid">';
                    images.forEach(image => {
                        html += `
                            <div class="image-preview-item">
                                <img src="../upload/${image.image_path}" alt="Destination Image">
                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;">
                                    <div style="text-align: center;">
                                        <button class="btn btn-sm btn-primary" onclick="setPrimaryImage(${image.id})" style="margin: 5px; ${image.is_primary ? 'display: none;' : ''}">
                                            <i class="ri-star-line"></i> Set Primary
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteImage(${image.id})" style="margin: 5px;">
                                            <i class="ri-delete-bin-line"></i> Delete
                                        </button>
                                        ${image.is_primary ? '<div style="background: var(--primary-color); padding: 5px 10px; border-radius: 10px; font-size: 0.8rem; color: white;">Primary Image</div>' : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    document.getElementById('images-list').innerHTML = html;
                    
                    // Add hover effect
                    document.querySelectorAll('#images-list .image-preview-item').forEach(item => {
                        item.onmouseover = function() {
                            this.children[1].style.opacity = '1';
                        };
                        item.onmouseout = function() {
                            this.children[1].style.opacity = '0';
                        };
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('images-list').innerHTML = '<p>No images found</p>';
                });
        }
        
        function setPrimaryImage(imageId) {
            if (confirm('Set this image as primary?')) {
                window.location.href = `?set_primary=${imageId}`;
            }
        } 
        
        function deleteImage(imageId) {
            if (confirm('Delete this image?')) {
                window.location.href = `?delete_image=${imageId}`;
            }
        }
        
        // Search and Filter
        function searchDestinations() {
            const searchTerm = document.getElementById('destination-search').value.toLowerCase();
            const rows = document.querySelectorAll('.destination-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }
        
        function filterDestinations(filter) {
            const rows = document.querySelectorAll('.destination-row');
            
            rows.forEach(row => {
                const rowRegion = row.getAttribute('data-region');
                const rowType = row.getAttribute('data-type');
                const rowStatus = row.getAttribute('data-status');
                const rowFeatured = row.getAttribute('data-featured');
                let show = false;
                
                switch(filter) {
                    case 'active':
                        show = rowStatus === 'active';
                        break;
                    case 'featured':
                        show = rowFeatured === 'featured';
                        break;
                    case 'kashmir':
                        show = rowRegion === 'kashmir';
                        break;
                    case 'ladakh':
                        show = rowRegion === 'ladakh';
                        break;
                    case 'lake':
                        show = rowType === 'lake';
                        break;
                    case 'adventure':
                        show = rowType === 'adventure';
                        break;
                    default:
                        show = true;
                }
                
                row.style.display = show ? '' : 'none';
            });
        }
        
        // Close modals
        function closeModal() {
            document.getElementById('destination-modal').classList.remove('active');
        }
        
        function closeImagesModal() {
            document.getElementById('images-modal').classList.remove('active');
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
        document.getElementById('destination-form').addEventListener('submit', function(e) {
            const requiredFields = ['destination_name', 'region', 'destination_type', 'location', 'short_description'];
            let isValid = true;
            
            requiredFields.forEach(field => {
                const element = document.getElementById(field);
                if (!element.value.trim()) {
                    isValid = false;
                    element.style.borderColor = 'var(--error-color)';
                } else {
                    element.style.borderColor = '';
                }
            });
            
            // Check at least one season is selected
            const seasonCheckboxes = document.querySelectorAll('input[name="best_seasons[]"]:checked');
            if (seasonCheckboxes.length === 0) {
                isValid = false;
                alert('Please select at least one best season.');
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields marked with *');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>