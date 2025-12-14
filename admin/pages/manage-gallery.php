<?php
session_start();
require_once '../includes/connection.php'; // Use your existing connection

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_image'])) {
        // Handle image upload
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $categories = isset($_POST['categories']) ? implode(' ', $_POST['categories']) : '';
        $display_order = intval($_POST['display_order']);
        $is_active = 1; // Default active
        
        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $uploadDir = '../uploads/gallery/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Generate unique filename
            $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $fileName = time() . '_' . uniqid() . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;
            
            // Check file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExt, $allowedTypes)) {
                // Check file size (5MB limit)
                if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $_SESSION['error'] = 'File size must be less than 5MB.';
                } else {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $imagePath = 'uploads/gallery/' . $fileName;
                        
                        // Insert into database
                        $sql = "INSERT INTO gallery (title, description, image_path, categories, display_order, is_active) 
                                VALUES ('$title', '$description', '$imagePath', '$categories', $display_order, $is_active)";
                        
                        if (mysqli_query($conn, $sql)) {
                            $_SESSION['success'] = 'Image added successfully!';
                            header('Location: manage-gallery.php');
                            exit();
                        } else {
                            $_SESSION['error'] = 'Database error: ' . mysqli_error($conn);
                            // Delete uploaded file if database insert fails
                            if (file_exists($targetPath)) {
                                unlink($targetPath);
                            }
                        }
                    } else {
                        $_SESSION['error'] = 'Failed to upload image.';
                    }
                }
            } else {
                $_SESSION['error'] = 'Invalid file type. Allowed: JPG, PNG, GIF, WEBP';
            }
        } else {
            $_SESSION['error'] = 'Please select an image file.';
        }
    } 
    elseif (isset($_POST['update_image'])) {
        $id = intval($_POST['id']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $categories = isset($_POST['categories']) ? implode(' ', $_POST['categories']) : '';
        $display_order = intval($_POST['display_order']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Check if new image is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            // Handle new image upload
            $uploadDir = '../upload/gallery/';
            $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $fileName = time() . '_' . uniqid() . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;
            
            // Check file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExt, $allowedTypes)) {
                // Check file size
                if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $_SESSION['error'] = 'File size must be less than 5MB.';
                    header('Location: manage-gallery.php');
                    exit();
                }
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = '../uploads/gallery/' . $fileName;
                    
                    // Get old image path
                    $oldImageQuery = "SELECT image_path FROM gallery WHERE id = $id";
                    $oldImageResult = mysqli_query($conn, $oldImageQuery);
                    if ($oldImageRow = mysqli_fetch_assoc($oldImageResult)) {
                        // Delete old image file
                        if ($oldImageRow['image_path'] && file_exists('../' . $oldImageRow['image_path'])) {
                            unlink('../' . $oldImageRow['image_path']);
                        }
                    }
                    
                    // Update with new image
                    $sql = "UPDATE gallery SET 
                            title = '$title', 
                            description = '$description', 
                            image_path = '$imagePath', 
                            categories = '$categories', 
                            display_order = $display_order, 
                            is_active = $is_active 
                            WHERE id = $id";
                } else {
                    $_SESSION['error'] = 'Failed to upload new image.';
                    header('Location: manage-gallery.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Invalid file type. Allowed: JPG, PNG, GIF, WEBP';
                header('Location: manage-gallery.php');
                exit();
            }
        } else {
            // Update without changing image
            $sql = "UPDATE gallery SET 
                    title = '$title', 
                    description = '$description', 
                    categories = '$categories', 
                    display_order = $display_order, 
                    is_active = $is_active 
                    WHERE id = $id";
        }
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success'] = 'Image updated successfully!';
        } else {
            $_SESSION['error'] = 'Database error: ' . mysqli_error($conn);
        }
        
        header('Location: manage-gallery.php');
        exit();
    } 
    elseif (isset($_POST['delete_image'])) {
        $id = intval($_POST['id']);
        
        // Get image path before deleting
        $sql = "SELECT image_path FROM gallery WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $imagePath = $row['image_path'];
            
            // Delete from database
            $deleteSql = "DELETE FROM gallery WHERE id = $id";
            if (mysqli_query($conn, $deleteSql)) {
                // Delete file from server
                if ($imagePath && file_exists('../' . $imagePath)) {
                    unlink('../' . $imagePath);
                }
                $_SESSION['success'] = 'Image deleted successfully!';
            } else {
                $_SESSION['error'] = 'Database error: ' . mysqli_error($conn);
            }
        } else {
            $_SESSION['error'] = 'Image not found!';
        }
        
        header('Location: manage-gallery.php');
        exit();
    }
}

// Fetch all gallery images
$sql = "SELECT * FROM gallery ORDER BY display_order ASC, created_at DESC";
$result = mysqli_query($conn, $sql);
$galleryItems = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $galleryItems[] = $row;
    }
}

// Define categories
$allCategories = ['kashmir', 'ladakh', 'lakes', 'mountains', 'culture', 'adventure'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - Zubi Tours Admin</title>
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

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb i {
            margin: 0 10px;
            font-size: 0.8rem;
        }

        .action-buttons {
            display: flex;
            gap: 16px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--primary-light);
            color: var(--primary-color);
            border-color: var(--primary-light);
        }

        /* Filters Section */
        .filters-section {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filters-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .filter-select,
        .filter-input {
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .filter-select:focus,
        .filter-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Destinations Grid */
        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .destination-card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }

        .destination-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .card-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .destination-card:hover .card-image img {
            transform: scale(1.05);
        }

        .card-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: var(--primary-color);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }

        .card-content {
            padding: 20px;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--text-primary);
        }

        .card-description {
            color: var(--text-secondary);
            margin-bottom: 20px;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--border-color);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .meta-item i {
            color: var(--primary-color);
        }

        .card-actions {
            display: flex;
            gap: 12px;
        }

        .card-btn {
            flex: 1;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .card-btn-edit {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
        }

        .card-btn-edit:hover {
            background: var(--primary-color);
            color: white;
        }

        .card-btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
        }

        .card-btn-delete:hover {
            background: var(--error-color);
            color: white;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-top: 32px;
        }

        .pagination-btn {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: var(--transition);
        }

        .pagination-btn:hover,
        .pagination-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-btn.disabled:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            width: 100%;
            max-width: 600px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
        }

        .modal-close:hover {
            color: var(--error-color);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 24px;
        }

        .modal-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-input,
        .form-textarea,
        .form-select {
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: var(--transition);
            font-family: inherit;
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .form-row {
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

            .destinations-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .destinations-grid {
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

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }

            .btn {
                flex: 1;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .card-actions {
                flex-direction: column;
            }

            .modal-content {
                margin: 0;
                border-radius: 0;
                height: 100%;
                overflow-y: auto;
            }

            .form-actions{
                    flex-direction: column;
            }
        }
  
        /* Your existing CSS styles remain the same */
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

        .content {
            padding: 32px;
        }

        /* Form Styles */
        .form-container {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--shadow);
            margin-bottom: 32px;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .form-container:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 5px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-size: 0.95rem;
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

        .btn-secondary {
            background: var(--secondary-color);
            color: white;
        }

        .btn-danger {
            background: var(--error-color);
            color: white;
        }

        /* Gallery Grid Styles */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }

        .gallery-card {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .gallery-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .gallery-info {
            padding: 20px;
        }

        .gallery-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .gallery-description {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .gallery-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 15px;
        }

        .category-tag {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .gallery-actions {
            display: flex;
            gap: 10px;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 0.85rem;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error-color);
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
        }

        .modal-content {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 30px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
        }

        .modal-close:hover {
            color: var(--error-color);
        }

        /* Message Styles */
        .message {
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .message-success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .message-error {
            background: rgba(239, 68, 68, 0.15);
            color: var(--error-color);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Image Preview */
        .image-preview {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
            border: 2px solid var(--border-color);
        }

        /* Upload Info */
        .upload-info {
            background: rgba(37, 99, 235, 0.05);
            border: 1px solid rgba(37, 99, 235, 0.1);
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .upload-info i {
            color: var(--primary-color);
            margin-right: 5px;
        }

        /* File Input Styling */
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-custom {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border: 2px dashed var(--border-color);
            border-radius: 10px;
            background: var(--bg-primary);
            transition: var(--transition);
            cursor: pointer;
        }

        .file-input-custom:hover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
        }

        .file-input-custom i {
            font-size: 1.2rem;
            margin-right: 10px;
            color: var(--primary-color);
        }

        .file-name {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            :root {
                --sidebar-width: 80px;
            }
            
            .main-content {
                margin-left: 80px;
                width: calc(100% - 80px);
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 15px;
            }
            
            .gallery-grid {
                grid-template-columns: 1fr;
            }
            
            .gallery-actions {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .header {
                padding: 0 20px;
            }
            
            .checkbox-group {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Include Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Include Header -->
        <?php include '../includes/header.php'; ?>

        <div class="content">
            <!-- Page Header -->
            <div class="page-header" style="margin-bottom: 30px;">
                <h1 class="page-title">Manage Gallery</h1>
                <p style="color: var(--text-secondary); margin-top: 10px;">Add, edit, or remove gallery images</p>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="message message-success">
                    <i class="ri-checkbox-circle-line"></i> <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="message message-error">
                    <i class="ri-error-warning-line"></i> <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Add New Image Form -->
            <div class="form-container">
                <h2 style="margin-bottom: 20px; color: var(--text-primary); display: flex; align-items: center; gap: 10px;">
                    <i class="ri-add-circle-line"></i> Add New Image
                </h2>
                <form action="" method="POST" enctype="multipart/form-data" id="addForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title"><i class="ri-image-line"></i> Image Title *</label>
                            <input type="text" id="title" name="title" class="form-control" required 
                                   placeholder="Enter image title">
                        </div>
                        <div class="form-group">
                            <label for="display_order"><i class="ri-list-ordered"></i> Display Order</label>
                            <input type="number" id="display_order" name="display_order" class="form-control" value="0" 
                                   placeholder="Order number">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description"><i class="ri-file-text-line"></i> Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3" 
                                  placeholder="Optional description"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="ri-price-tag-3-line"></i> Categories *</label>
                        <div class="checkbox-group">
                            <?php foreach ($allCategories as $category): ?>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="categories[]" value="<?= $category ?>">
                                    <span style="text-transform: capitalize;"><?= $category ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="ri-upload-line"></i> Image *</label>
                        <div class="file-input-wrapper">
                            <div class="file-input-custom">
                                <i class="ri-image-add-line"></i>
                                <span class="file-name" id="fileName">Choose an image file...</span>
                                <i class="ri-folder-open-line"></i>
                            </div>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*" required
                                   onchange="updateFileName(this)">
                        </div>
                        
                        <div class="upload-info">
                            <i class="ri-information-line"></i>
                            <strong>Upload Requirements:</strong><br>
                            • Allowed formats: JPG, PNG, GIF, WEBP<br>
                            • Maximum file size: 5MB<br>
                            • Images will be saved to: <code>uploads/gallery/</code><br>
                            • Recommended dimensions: 800x600px or larger
                        </div>
                        
                        <img id="preview" class="image-preview" style="display: none;">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="add_image" class="btn btn-primary">
                            <i class="ri-upload-line"></i> Upload Image
                        </button>
                        <button type="reset" class="btn btn-secondary" onclick="clearPreview()">
                            <i class="ri-refresh-line"></i> Reset Form
                        </button>
                    </div>
                </form>
            </div>

            <!-- Gallery Images Grid -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: var(--text-primary); display: flex; align-items: center; gap: 10px;">
                    <i class="ri-gallery-line"></i> Gallery Images <span style="font-size: 0.9em; color: var(--text-secondary);">(<?= count($galleryItems) ?>)</span>
                </h2>
                <?php if (!empty($galleryItems)): ?>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">
                        <i class="ri-folder-line"></i> Location: <code>uploads/gallery/</code>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (empty($galleryItems)): ?>
                <div class="form-container" style="text-align: center; padding: 40px;">
                    <i class="ri-image-line" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 20px;"></i>
                    <h3 style="color: var(--text-secondary); margin-bottom: 10px;">No Images Found</h3>
                    <p style="color: var(--text-secondary);">Upload your first image using the form above.</p>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 10px;">
                        <i class="ri-information-line"></i> Images will be saved to: <code>uploads/gallery/</code> folder
                    </p>
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($galleryItems as $item): 
                        $imagePath = '../' . $item['image_path'];
                        // Check if file exists
                        $fileExists = file_exists($imagePath);
                        $fileSize = $fileExists ? filesize($imagePath) : 0;
                        $fileSizeFormatted = $fileSize ? round($fileSize / 1024, 1) . ' KB' : 'N/A';
                    ?>
                    <div class="gallery-card">
                        <div style="position: relative;">
                            <img src="<?= htmlspecialchars($imagePath) ?>" 
                                 alt="<?= htmlspecialchars($item['title']) ?>" 
                                 class="gallery-image"
                                 onerror="this.src='https://via.placeholder.com/400x300?text=Image+Not+Found'">
                            <div style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">
                                #<?= $item['display_order'] ?>
                            </div>
                            <?php if (!$fileExists): ?>
                                <div style="position: absolute; top: 10px; left: 10px; background: rgba(239, 68, 68, 0.9); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">
                                    <i class="ri-error-warning-line"></i> File Missing
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="gallery-info">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <span class="status-badge <?= $item['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                    <?= $item['is_active'] ? '✓ Active' : '✗ Inactive' ?>
                                </span>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                    <i class="ri-file-info-line"></i> <?= $fileSizeFormatted ?>
                                </div>
                            </div>
                            
                            <h3 class="gallery-title"><?= htmlspecialchars($item['title']) ?></h3>
                            
                            <?php if ($item['description']): ?>
                                <p class="gallery-description"><?= htmlspecialchars($item['description']) ?></p>
                            <?php endif; ?>
                            
                            <div class="gallery-categories">
                                <?php 
                                $categories = explode(' ', $item['categories']);
                                foreach ($categories as $cat):
                                    if (trim($cat)): ?>
                                        <span class="category-tag"><?= htmlspecialchars(ucfirst($cat)) ?></span>
                                    <?php endif;
                                endforeach; ?>
                            </div>
                            
                            <div class="gallery-meta" style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 15px;">
                                <div><i class="ri-calendar-line"></i> Added: <?= date('M d, Y', strtotime($item['created_at'])) ?></div>
                                <?php if ($item['updated_at'] != $item['created_at']): ?>
                                    <div><i class="ri-refresh-line"></i> Updated: <?= date('M d, Y', strtotime($item['updated_at'])) ?></div>
                                <?php endif; ?>
                                <div style="font-family: monospace; font-size: 0.8rem; margin-top: 5px; background: var(--bg-primary); padding: 4px 8px; border-radius: 4px;">
                                    <i class="ri-folder-line"></i> <?= htmlspecialchars($item['image_path']) ?>
                                </div>
                            </div>
                            
                            <div class="gallery-actions">
                                <button type="button" class="btn btn-primary btn-sm edit-btn" 
                                        data-id="<?= $item['id'] ?>"
                                        data-title="<?= htmlspecialchars($item['title']) ?>"
                                        data-description="<?= htmlspecialchars($item['description']) ?>"
                                        data-categories='<?= json_encode(explode(' ', $item['categories'])) ?>'
                                        data-display_order="<?= $item['display_order'] ?>"
                                        data-is_active="<?= $item['is_active'] ?>"
                                        data-image_path="<?= htmlspecialchars($item['image_path']) ?>">
                                    <i class="ri-edit-line"></i> Edit
                                </button>
                                
                                <form action="" method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="delete_image" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Are you sure you want to delete this image? This will remove both the database entry and the file from uploads/gallery/ folder.')">
                                        <i class="ri-delete-bin-line"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="ri-edit-line"></i> Edit Image</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="editForm">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="update_image" value="1">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editTitle"><i class="ri-image-line"></i> Image Title *</label>
                        <input type="text" id="editTitle" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="editDisplayOrder"><i class="ri-list-ordered"></i> Display Order</label>
                        <input type="number" id="editDisplayOrder" name="display_order" class="form-control" value="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="editDescription"><i class="ri-file-text-line"></i> Description</label>
                    <textarea id="editDescription" name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="ri-price-tag-3-line"></i> Categories *</label>
                    <div class="checkbox-group" id="editCategories">
                        <?php foreach ($allCategories as $category): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="categories[]" value="<?= $category ?>">
                                <span style="text-transform: capitalize;"><?= $category ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="editIsActive"><i class="ri-toggle-line"></i> Status</label>
                        <select id="editIsActive" name="is_active" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="ri-image-add-line"></i> New Image</label>
                        <div class="file-input-wrapper">
                            <div class="file-input-custom">
                                <i class="ri-image-add-line"></i>
                                <span class="file-name" id="editFileName">Keep current image or choose new...</span>
                                <i class="ri-folder-open-line"></i>
                            </div>
                            <input type="file" id="editImage" name="image" class="form-control" accept="image/*"
                                   onchange="updateEditFileName(this)">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="ri-image-line"></i> Current Image</label>
                    <div style="background: var(--bg-primary); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                        <img id="currentImage" class="image-preview" style="display: block; margin-bottom: 10px;">
                        <div style="font-size: 0.9rem; color: var(--text-secondary); font-family: monospace;">
                            <i class="ri-folder-line"></i> <span id="currentImagePath"></span>
                        </div>
                    </div>
                    <small style="color: var(--text-secondary); display: block; margin-top: 5px;">
                        <i class="ri-information-line"></i> Leave new image field empty to keep current image
                    </small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line"></i> Update Image
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        <i class="ri-close-line"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update file name display
            function updateFileName(input) {
                const fileName = input.files[0] ? input.files[0].name : 'Choose an image file...';
                document.getElementById('fileName').textContent = fileName;
                
                // Preview image
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('preview');
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
            
            function updateEditFileName(input) {
                const fileName = input.files[0] ? input.files[0].name : 'Keep current image or choose new...';
                document.getElementById('editFileName').textContent = fileName;
            }
            
            window.updateFileName = updateFileName;
            window.updateEditFileName = updateEditFileName;
            
            // Clear preview
            function clearPreview() {
                const preview = document.getElementById('preview');
                preview.style.display = 'none';
                preview.src = '';
                document.getElementById('fileName').textContent = 'Choose an image file...';
            }
            window.clearPreview = clearPreview;
            
            // Edit button click handlers
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const title = this.dataset.title;
                    const description = this.dataset.description;
                    const categories = JSON.parse(this.dataset.categories || '[]');
                    const displayOrder = this.dataset.display_order;
                    const isActive = this.dataset.is_active;
                    const imagePath = this.dataset.image_path;
                    
                    // Set form values
                    document.getElementById('editId').value = id;
                    document.getElementById('editTitle').value = title;
                    document.getElementById('editDescription').value = description;
                    document.getElementById('editDisplayOrder').value = displayOrder;
                    document.getElementById('editIsActive').value = isActive;
                    
                    // Show current image
                    const currentImage = document.getElementById('currentImage');
                    const currentImagePath = document.getElementById('currentImagePath');
                    if (imagePath) {
                        currentImage.src = '../' + imagePath;
                        currentImage.style.display = 'block';
                        currentImagePath.textContent = imagePath;
                    }
                    
                    // Reset checkboxes
                    document.querySelectorAll('#editCategories input[type="checkbox"]').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    
                    // Set category checkboxes
                    categories.forEach(category => {
                        if (category.trim()) {
                            const checkbox = document.querySelector(`#editCategories input[value="${category.trim()}"]`);
                            if (checkbox) checkbox.checked = true;
                        }
                    });
                    
                    // Reset file input
                    document.getElementById('editImage').value = '';
                    document.getElementById('editFileName').textContent = 'Keep current image or choose new...';
                    
                    // Show modal
                    document.getElementById('editModal').style.display = 'flex';
                });
            });
            
            // Close modal on outside click
            document.getElementById('editModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
            
            // Form validation
            document.getElementById('addForm').addEventListener('submit', function(e) {
                // Check categories
                const checkboxes = this.querySelectorAll('input[name="categories[]"]:checked');
                if (checkboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one category.');
                    return false;
                }
                
                // Check file
                const fileInput = this.querySelector('input[type="file"]');
                if (fileInput.files.length === 0) {
                    e.preventDefault();
                    alert('Please select an image file.');
                    return false;
                }
                
                // Check file size (5MB limit)
                const file = fileInput.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    e.preventDefault();
                    alert('File size must be less than 5MB.');
                    return false;
                }
            });
            
            document.getElementById('editForm').addEventListener('submit', function(e) {
                // Check categories
                const checkboxes = this.querySelectorAll('input[name="categories[]"]:checked');
                if (checkboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one category.');
                    return false;
                }
                
                // Check new file size if selected
                const fileInput = this.querySelector('input[type="file"]');
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    if (file.size > 5 * 1024 * 1024) {
                        e.preventDefault();
                        alert('File size must be less than 5MB.');
                        return false;
                    }
                }
            });
            
            // Dark mode support
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    document.body.classList.toggle('dark-mode');
                    const themeIcon = this.querySelector('i');
                    if (document.body.classList.contains('dark-mode')) {
                        themeIcon.classList.replace('ri-moon-line', 'ri-sun-line');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        themeIcon.classList.replace('ri-sun-line', 'ri-moon-line');
                        localStorage.setItem('theme', 'light');
                    }
                });
                
                // Check saved theme
                if (localStorage.getItem('theme') === 'dark') {
                    document.body.classList.add('dark-mode');
                    const themeIcon = themeToggle.querySelector('i');
                    if (themeIcon) themeIcon.classList.replace('ri-moon-line', 'ri-sun-line');
                }
            }
        });
        
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        window.closeModal = closeModal;
        
        // Handle image errors in gallery
        document.querySelectorAll('.gallery-image').forEach(img => {
            img.addEventListener('error', function() {
                this.src = 'https://via.placeholder.com/400x300?text=Image+Not+Found';
                this.style.border = '2px solid var(--error-color)';
            });
        });
    </script>
</body>
</html>