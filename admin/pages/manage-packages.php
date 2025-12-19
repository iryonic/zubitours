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

// Add new package
if (isset($_POST['add_package'])) {
    $package_name = $_POST['package_name'];
    $package_type = $_POST['package_type'];
    $duration_days = $_POST['duration_days'];
    $max_people = $_POST['max_people'];
    $accommodation_type = $_POST['accommodation_type'];
    $price_per_person = $_POST['price_per_person'];
    $description = $_POST['description'];
    $badge = $_POST['badge'];
    $rating = $_POST['rating'];
    $reviews_count = $_POST['reviews_count'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle JSON fields
    $highlights = [];
    $inclusions = [];
    $exclusions = [];
    $faqs = [];
    
    // Process highlights
    if (isset($_POST['highlight_titles']) && isset($_POST['highlight_descriptions'])) {
        $titles = $_POST['highlight_titles'];
        $descriptions = $_POST['highlight_descriptions'];
        for ($i = 0; $i < count($titles); $i++) {
            if (!empty(trim($titles[$i])) && !empty(trim($descriptions[$i]))) {
                $highlights[] = [
                    'title' => $titles[$i],
                    'description' => $descriptions[$i]
                ];
            }
        }
    }
    
    // Process inclusions
    if (isset($_POST['inclusions'])) {
        foreach ($_POST['inclusions'] as $inclusion) {
            if (!empty(trim($inclusion))) {
                $inclusions[] = $inclusion;
            }
        }
    }
    
    // Process exclusions
    if (isset($_POST['exclusions'])) {
        foreach ($_POST['exclusions'] as $exclusion) {
            if (!empty(trim($exclusion))) {
                $exclusions[] = $exclusion;
            }
        }
    }
    
    // Process FAQs
    if (isset($_POST['faq_questions']) && isset($_POST['faq_answers'])) {
        $questions = $_POST['faq_questions'];
        $answers = $_POST['faq_answers'];
        for ($i = 0; $i < count($questions); $i++) {
            if (!empty(trim($questions[$i])) && !empty(trim($answers[$i]))) {
                $faqs[] = [
                    'question' => $questions[$i],
                    'answer' => $answers[$i]
                ];
            }
        }
    }
    
    // Convert to JSON
    $highlights_json = json_encode($highlights);
    $inclusions_json = json_encode($inclusions);
    $exclusions_json = json_encode($exclusions);
    $faqs_json = json_encode($faqs);
    
    // Handle itinerary (multi-day)
    $itinerary = [];
    if (isset($_POST['day_numbers']) && isset($_POST['day_titles']) && isset($_POST['day_descriptions'])) {
        $day_numbers = $_POST['day_numbers'];
        $day_titles = $_POST['day_titles'];
        $day_descriptions = $_POST['day_descriptions'];
        $day_activities = $_POST['day_activities'] ?? [];
        
        for ($i = 0; $i < count($day_numbers); $i++) {
            if (!empty(trim($day_titles[$i]))) {
                $day_activities_list = [];
                if (isset($day_activities[$i])) {
                    foreach ($day_activities[$i] as $activity_time => $activity_desc) {
                        if (!empty(trim($activity_time)) && !empty(trim($activity_desc))) {
                            $day_activities_list[] = [
                                'time' => $activity_time,
                                'description' => $activity_desc
                            ];
                        }
                    }
                }
                
                $itinerary[] = [
                    'day' => $day_numbers[$i],
                    'title' => $day_titles[$i],
                    'description' => $day_descriptions[$i] ?? '',
                    'activities' => $day_activities_list
                ];
            }
        }
    }
    $itinerary_json = json_encode($itinerary);
    
    $stmt = $conn->prepare("INSERT INTO packages (package_name, package_type, duration_days, max_people, accommodation_type, price_per_person, description, highlights, inclusions, exclusions, faqs, itinerary, badge, rating, reviews_count, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiissdssssssdsii", $package_name, $package_type, $duration_days, $max_people, $accommodation_type, $price_per_person, $description, $highlights_json, $inclusions_json, $exclusions_json, $faqs_json, $itinerary_json, $badge, $rating, $reviews_count, $is_featured, $is_active);
    
    if ($stmt->execute()) {
        $package_id = $conn->insert_id;
        
        // Handle multiple image uploads
        if (isset($_FILES['package_images']) && !empty($_FILES['package_images']['name'][0])) {
            $upload_dir = '../upload/packages/';
            
            foreach ($_FILES['package_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['package_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = uniqid() . '_' . basename($_FILES['package_images']['name'][$key]);
                    $target_file = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $image_path = 'packages/' . $file_name;
                        $is_primary = ($key === 0) ? 1 : 0;
                        
                        $img_stmt = $conn->prepare("INSERT INTO package_images (package_id, image_path, is_primary) VALUES (?, ?, ?)");
                        $img_stmt->bind_param("iss", $package_id, $image_path, $is_primary);
                        $img_stmt->execute();
                    }
                }
            }
        }
        
        $message = "Package added successfully!";
        $message_type = "success";
    } else {
        $message = "Error adding package: " . $conn->error;
        $message_type = "error";
    }
}

// Update package
if (isset($_POST['update_package'])) {
    $id = $_POST['package_id'];
    $package_name = $_POST['package_name'];
    $package_type = $_POST['package_type'];
    $duration_days = $_POST['duration_days'];
    $max_people = $_POST['max_people'];
    $accommodation_type = $_POST['accommodation_type'];
    $price_per_person = $_POST['price_per_person'];
    $description = $_POST['description'];
    $badge = $_POST['badge'];
    $rating = $_POST['rating'];
    $reviews_count = $_POST['reviews_count'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Handle JSON fields (same as add)
    $highlights = [];
    $inclusions = [];
    $exclusions = [];
    $faqs = [];
    
    if (isset($_POST['highlight_titles']) && isset($_POST['highlight_descriptions'])) {
        $titles = $_POST['highlight_titles'];
        $descriptions = $_POST['highlight_descriptions'];
        for ($i = 0; $i < count($titles); $i++) {
            if (!empty(trim($titles[$i])) && !empty(trim($descriptions[$i]))) {
                $highlights[] = [
                    'title' => $titles[$i],
                    'description' => $descriptions[$i]
                ];
            }
        }
    }
    
    if (isset($_POST['inclusions'])) {
        foreach ($_POST['inclusions'] as $inclusion) {
            if (!empty(trim($inclusion))) {
                $inclusions[] = $inclusion;
            }
        }
    }
    
    if (isset($_POST['exclusions'])) {
        foreach ($_POST['exclusions'] as $exclusion) {
            if (!empty(trim($exclusion))) {
                $exclusions[] = $exclusion;
            }
        }
    }
    
    if (isset($_POST['faq_questions']) && isset($_POST['faq_answers'])) {
        $questions = $_POST['faq_questions'];
        $answers = $_POST['faq_answers'];
        for ($i = 0; $i < count($questions); $i++) {
            if (!empty(trim($questions[$i])) && !empty(trim($answers[$i]))) {
                $faqs[] = [
                    'question' => $questions[$i],
                    'answer' => $answers[$i]
                ];
            }
        }
    }
    
    // Handle itinerary
    $itinerary = [];
    if (isset($_POST['day_numbers']) && isset($_POST['day_titles']) && isset($_POST['day_descriptions'])) {
        $day_numbers = $_POST['day_numbers'];
        $day_titles = $_POST['day_titles'];
        $day_descriptions = $_POST['day_descriptions'];
        $day_activities = $_POST['day_activities'] ?? [];
        
        for ($i = 0; $i < count($day_numbers); $i++) {
            if (!empty(trim($day_titles[$i]))) {
                $day_activities_list = [];
                if (isset($day_activities[$i])) {
                    foreach ($day_activities[$i] as $activity_time => $activity_desc) {
                        if (!empty(trim($activity_time)) && !empty(trim($activity_desc))) {
                            $day_activities_list[] = [
                                'time' => $activity_time,
                                'description' => $activity_desc
                            ];
                        }
                    }
                }
                
                $itinerary[] = [
                    'day' => $day_numbers[$i],
                    'title' => $day_titles[$i],
                    'description' => $day_descriptions[$i] ?? '',
                    'activities' => $day_activities_list
                ];
            }
        }
    }
    
    $highlights_json = json_encode($highlights);
    $inclusions_json = json_encode($inclusions);
    $exclusions_json = json_encode($exclusions);
    $faqs_json = json_encode($faqs);
    $itinerary_json = json_encode($itinerary);
    
    $stmt = $conn->prepare("UPDATE packages SET package_name = ?, package_type = ?, duration_days = ?, max_people = ?, accommodation_type = ?, price_per_person = ?, description = ?, highlights = ?, inclusions = ?, exclusions = ?, faqs = ?, itinerary = ?, badge = ?, rating = ?, reviews_count = ?, is_featured = ?, is_active = ? WHERE id = ?");
    $stmt->bind_param("ssiissdssssssdsiii", $package_name, $package_type, $duration_days, $max_people, $accommodation_type, $price_per_person, $description, $highlights_json, $inclusions_json, $exclusions_json, $faqs_json, $itinerary_json, $badge, $rating, $reviews_count, $is_featured, $is_active, $id);
    
    if ($stmt->execute()) {
        // Handle any newly uploaded images during update
        $package_id = $id;
        if (isset($_FILES['package_images']) && !empty($_FILES['package_images']['name'][0])) {
            $upload_dir = '../upload/packages/';
            foreach ($_FILES['package_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['package_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = uniqid() . '_' . basename($_FILES['package_images']['name'][$key]);
                    $target_file = $upload_dir . $file_name;
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $image_path = 'packages/' . $file_name;
                        $is_primary = ($key === 0) ? 1 : 0;
                        $img_stmt = $conn->prepare("INSERT INTO package_images (package_id, image_path, is_primary) VALUES (?, ?, ?)");
                        $img_stmt->bind_param("iss", $package_id, $image_path, $is_primary);
                        $img_stmt->execute();
                    }
                }
            }
        }

        $message = "Package updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating package: " . $conn->error;
        $message_type = "error";
    }
}

// Delete package
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // 1. First delete all related bookings
        $stmt = $conn->prepare("DELETE FROM package_bookings WHERE package_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // 2. Get images to delete files
        $stmt = $conn->prepare("SELECT image_path FROM package_images WHERE package_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Delete image files
        while ($image = $result->fetch_assoc()) {
            $file_path = '../upload/' . $image['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        // 3. Delete image records
        $stmt = $conn->prepare("DELETE FROM package_images WHERE package_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // 4. Get package name for testimonials
        $stmt = $conn->prepare("SELECT package_name FROM packages WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $package = $stmt->get_result()->fetch_assoc();
        
        // 5. Delete testimonials
        if ($package) {
            $stmt = $conn->prepare("DELETE FROM testimonials WHERE package_name = ?");
            $stmt->bind_param("s", $package['package_name']);
            $stmt->execute();
        }
        
        // 6. Finally delete the package
        $stmt = $conn->prepare("DELETE FROM packages WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['message'] = "Package deleted successfully!";
        $_SESSION['message_type'] = "success";
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $_SESSION['message'] = "Error deleting package: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: manage-packages.php");
    exit();
}

// Delete image
if (isset($_GET['delete_image'])) {
    $image_id = $_GET['delete_image'];
    
    // Get image path to delete file
    $image_result = $conn->query("SELECT image_path FROM package_images WHERE id = $image_id");
    if ($image_result->num_rows > 0) {
        $image = $image_result->fetch_assoc();
        $file_path = '../upload/packages/' . $image['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    if ($conn->query("DELETE FROM package_images WHERE id = $image_id")) {
        $message = "Image deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting image: " . $conn->error;
        $message_type = "error";
    }
}

// Set primary image
if (isset($_GET['set_primary'])) {
    $image_id = $_GET['set_primary'];
    
    $img_result = $conn->query("SELECT package_id FROM package_images WHERE id = $image_id");
    if ($img_result->num_rows > 0) {
        $img = $img_result->fetch_assoc();
        $package_id = $img['package_id'];
        
        $conn->query("UPDATE package_images SET is_primary = 0 WHERE package_id = $package_id");
        
        if ($conn->query("UPDATE package_images SET is_primary = 1 WHERE id = $image_id")) {
            $message = "Primary image updated successfully!";
            $message_type = "success";
        }
    }
}

// Update booking status
if (isset($_POST['update_booking_status'])) {
    $booking_id = $_POST['booking_id'];
    $booking_status = $_POST['booking_status'];
    $payment_status = $_POST['payment_status'];
    $notes = $_POST['notes'];
    
    $stmt = $conn->prepare("UPDATE package_bookings SET booking_status = ?, payment_status = ?, notes = ? WHERE id = ?");
    $stmt->bind_param("sssi", $booking_status, $payment_status, $notes, $booking_id);
    
    if ($stmt->execute()) {
        $message = "Booking status updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating booking: " . $conn->error;
        $message_type = "error";
    }
}

// Bulk actions
if (isset($_POST['bulk_action'])) {
    $action = $_POST['bulk_action'];
    $selected_packages = $_POST['selected_packages'] ?? [];
    
    if (!empty($selected_packages)) {
        $ids = implode(',', array_map('intval', $selected_packages));
        
        switch ($action) {
            case 'activate':
                $conn->query("UPDATE packages SET is_active = 1 WHERE id IN ($ids)");
                $message = "Selected packages activated!";
                break;
            case 'deactivate':
                $conn->query("UPDATE packages SET is_active = 0 WHERE id IN ($ids)");
                $message = "Selected packages deactivated!";
                break;
            case 'feature':
                $conn->query("UPDATE packages SET is_featured = 1 WHERE id IN ($ids)");
                $message = "Selected packages marked as featured!";
                break;
            case 'unfeature':
                $conn->query("UPDATE packages SET is_featured = 0 WHERE id IN ($ids)");
                $message = "Selected packages unfeatured!";
                break;
            case 'delete':
                // Delete images first
                $images = $conn->query("SELECT image_path FROM package_images WHERE package_id IN ($ids)");
                while ($image = $images->fetch_assoc()) {
                    $file_path = '../../assets/img/' . $image['image_path'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
                $conn->query("DELETE FROM package_images WHERE package_id IN ($ids)");
                $conn->query("DELETE FROM packages WHERE id IN ($ids)");
                $message = "Selected packages deleted!";
                break;
        }
        $message_type = "success";
    }
}

// Fetch all packages with their primary images and stats
$packages = $conn->query("
    SELECT p.*, pi.image_path, 
           (SELECT COUNT(*) FROM package_bookings pb WHERE pb.package_id = p.id) as total_bookings,
           (SELECT COUNT(*) FROM package_bookings pb WHERE pb.package_id = p.id AND pb.booking_status = 'pending') as pending_bookings
    FROM packages p 
    LEFT JOIN package_images pi ON p.id = pi.package_id AND pi.is_primary = 1 
    ORDER BY p.created_at DESC
");

// Fetch all bookings with package info
$bookings = $conn->query("
    SELECT pb.*, p.package_name, p.duration_days, p.price_per_person,
           DATEDIFF(pb.checkout_date, pb.checkin_date) as booked_days
    FROM package_bookings pb 
    JOIN packages p ON pb.package_id = p.id 
    ORDER BY pb.booked_at DESC
");

// Get stats
$total_packages = $conn->query("SELECT COUNT(*) as count FROM packages")->fetch_assoc()['count'];
$active_packages = $conn->query("SELECT COUNT(*) as count FROM packages WHERE is_active = 1")->fetch_assoc()['count'];
$featured_packages = $conn->query("SELECT COUNT(*) as count FROM packages WHERE is_featured = 1")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM package_bookings")->fetch_assoc()['count'];
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM package_bookings WHERE booking_status = 'pending'")->fetch_assoc()['count'];
$revenue = $conn->query("SELECT SUM(total_amount) as total FROM package_bookings WHERE booking_status IN ('confirmed', 'completed') AND payment_status = 'paid'")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tour Packages - Zubi Tours Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
      <link rel="stylesheet" href="../assets/admin.css">
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
                <h1 class="section-title">Manage Tour Packages</h1>
                <div>
                    <button class="btn btn-primary" onclick="openAddPackageModal()">
                        <i class="ri-add-line"></i> Add New Package
                    </button>
                    <button class="btn btn-secondary" onclick="exportPackages()" style="margin-left: 10px;">
                        <i class="ri-download-line"></i> Export
                    </button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <button class="quick-action-btn" onclick="filterPackages('active')">
                    <i class="ri-checkbox-circle-line"></i> Active
                </button>
                <button class="quick-action-btn" onclick="filterPackages('featured')">
                    <i class="ri-star-line"></i> Featured
                </button>
                <button class="quick-action-btn" onclick="filterPackages('cultural')">
                    <i class="ri-building-2-line"></i> Cultural
                </button>
                <button class="quick-action-btn" onclick="filterPackages('adventure')">
                    <i class="ri-map-pin-line"></i> Adventure
                </button>
                <button class="quick-action-btn" onclick="showStats()">
                    <i class="ri-bar-chart-line"></i> Statistics
                </button>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon cars-icon">
                        <i class="ri-briefcase-4-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_packages; ?></h3>
                        <p>Total Packages</p>
                        <div class="stat-trend">
                            <span><?php echo $active_packages; ?> active</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bookings-icon">
                        <i class="ri-calendar-check-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_bookings; ?></h3>
                        <p>Total Bookings</p>
                        <div class="stat-trend">
                            <span><?php echo $pending_bookings; ?> pending</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon revenue-icon">
                        <i class="ri-money-rupee-circle-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>₹<?php echo number_format($revenue, 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon available-icon">
                        <i class="ri-star-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $featured_packages; ?></h3>
                        <p>Featured Packages</p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" onclick="switchTab('packages')">
                    Packages List
                    <span class="tab-badge"><?php echo $total_packages; ?></span>
                </div>
                <div class="tab" onclick="switchTab('bookings')">
                    Bookings
                    <span class="tab-badge"><?php echo $total_bookings; ?></span>
                </div>
                <div class="tab" onclick="switchTab('images')">
                    Media Library
                </div>
            </div>

            <!-- Packages List Tab -->
            <div id="packages-tab" class="tab-content active">
                <!-- Search and Filters -->
                <div class="search-container">
                    <i class="ri-search-line"></i>
                    <input type="text" id="package-search" placeholder="Search packages by name, type, or description..." onkeyup="searchPackages()">
                </div>

                <!-- Bulk Actions
                <form method="POST" action="" id="bulk-form" class="bulk-actions">
                    <input type="checkbox" id="select-all" class="bulk-checkbox" onchange="toggleSelectAll()">
                    <label for="select-all">Select All</label>
                    
                    <select name="bulk_action" class="bulk-select" required>
                        <option value="">Bulk Actions</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                        <option value="feature">Mark as Featured</option>
                        <option value="unfeature">Remove Featured</option>
                        <option value="delete">Delete</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary btn-sm" onclick="return confirmBulkAction()">
                        <i class="ri-play-line"></i> Apply
                    </button>
                    
                    <div style="margin-left: auto; display: flex; gap: 10px;">
                        <select id="filter-type" class="filter-select" onchange="filterByType()" style="padding: 8px; border-radius: 8px;">
                            <option value="">All Types</option>
                            <option value="cultural">Cultural</option>
                            <option value="adventure">Adventure</option>
                            <option value="luxury">Luxury</option>
                            <option value="honeymoon">Honeymoon</option>
                            <option value="family">Family</option>
                        </select>
                        
                        <select id="filter-status" class="filter-select" onchange="filterByStatus()" style="padding: 8px; border-radius: 8px;">
                            <option value="">All Status</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                            <option value="featured">Featured Only</option>
                        </select>
                    </div>
                </form> -->

                <!-- Packages Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th width="300">Package Details</th>
                                <th>Type</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Bookings</th>
                                <th>Status</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="packages-table-body">
                            <?php 
                            $counter = 1;
                            while ($package = $packages->fetch_assoc()): 
                                $highlights = json_decode($package['highlights'], true) ?: [];
                                $firstHighlight = !empty($highlights) ? $highlights[0]['description'] : substr($package['description'], 0, 100) . '...';
                            ?>
                                <tr class="package-row" 
                                    data-type="<?php echo $package['package_type']; ?>"
                                    data-status="<?php echo $package['is_active'] ? 'active' : 'inactive'; ?>"
                                    data-featured="<?php echo $package['is_featured'] ? 'featured' : 'not-featured'; ?>">
                                    <td>
                                        <input type="checkbox" name="selected_packages[]" value="<?php echo $package['id']; ?>" class="package-checkbox">
                                    </td>
                                    <td class="package-name-cell">
                                        <div style="display: flex; gap: 15px; align-items: flex-start;">
                                            <div style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden; flex-shrink: 0;">
                                                <img src="../upload/<?php echo $package['image_path'] ?: 'bg1.jpg'; ?>" 
                                                     alt="<?php echo htmlspecialchars($package['package_name']); ?>"
                                                     style="width: 100%; height: 100%; object-fit: cover;"
                                                     onerror="this.src='../../assets/img/bg1.jpg'">
                                                     
                                            </div>
                                            <div>
                                                <h4><?php echo htmlspecialchars($package['package_name']); ?></h4>
                                                <p><?php echo htmlspecialchars($firstHighlight); ?></p>
                                                <div style="display: flex; gap: 5px; margin-top: 5px;">
                                                    <?php if ($package['badge']): ?>
                                                        <span class="status-badge" style="background: rgba(37, 99, 235, 0.15); color: var(--primary-color); font-size: 0.7rem;">
                                                            <?php echo $package['badge']; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <span class="status-badge" style="background: rgba(100, 116, 139, 0.15); color: var(--secondary-color); font-size: 0.7rem;">
                                                        <i class="ri-star-fill" style="font-size: 0.7rem;"></i>
                                                        <?php echo $package['rating'] ? number_format($package['rating'], 1) : '4.9'; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="type-<?php echo $package['package_type']; ?>" style="font-weight: 600;">
                                            <?php echo ucfirst($package['package_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--primary-color);">
                                            <?php echo $package['duration_days']; ?> Days
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                            Max <?php echo $package['max_people']; ?> people
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--primary-color);">
                                            ₹<?php echo number_format($package['price_per_person'], 2); ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                            per person
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;">
                                            <?php echo $package['total_bookings']; ?> total
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--warning-color);">
                                            <?php echo $package['pending_bookings']; ?> pending
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($package['is_active']): ?>
                                            <span class="status-badge status-active">Active</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">Inactive</span>
                                        <?php endif; ?>
                                        
                                        <?php if ($package['is_featured']): ?>
                                            <br><span class="status-badge status-featured" style="margin-top: 5px;">Featured</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="btn btn-primary btn-sm" onclick="editPackage(<?php echo $package['id']; ?>)">
                                                <i class="ri-edit-line"></i> Edit
                                            </button>
                                            <button class="btn btn-warning btn-sm" onclick="manageImages(<?php echo $package['id']; ?>)">
                                                <i class="ri-image-line"></i> Images
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="deletePackage(<?php echo $package['id']; ?>)">
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
                    
                    <?php if ($packages->num_rows === 0): ?>
                        <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                            <i class="ri-inbox-line" style="font-size: 3rem; margin-bottom: 15px;"></i>
                            <h3>No packages found</h3>
                            <p>Click "Add New Package" to create your first tour package.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bookings Tab -->
            <div id="bookings-tab" class="tab-content">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <th>Package</th>
                                <th>Customer</th>
                                <th>Dates</th>
                                <th>Guests</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $bookings->fetch_assoc()): 
                                $totalAmount = $booking['total_amount'] ?? ($booking['price_per_person'] * ($booking['number_of_adults'] + ($booking['number_of_children'] * 0.7)));
                            ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $booking['booking_reference']; ?></strong><br>
                                        <small style="color: var(--text-secondary); font-size: 0.8rem;">
                                            <?php echo date('M d, Y', strtotime($booking['booked_at'])); ?>
                                        </small>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong><br>
                                        <small><?php echo $booking['customer_email']; ?></small><br>
                                        <small><?php echo $booking['customer_phone']; ?></small>
                                    </td>
                                    <td>
                                        <?php echo date('M d', strtotime($booking['checkin_date'])); ?> -<br>
                                        <?php echo date('M d, Y', strtotime($booking['checkout_date'])); ?><br>
                                        <small><?php echo $booking['booked_days'] ?? $booking['duration_days']; ?> days</small>
                                    </td>
                                    <td>
                                        <?php echo $booking['number_of_adults']; ?> Adults<br>
                                        <?php echo $booking['number_of_children']; ?> Children
                                    </td>
                                    <td>
                                        <strong>₹<?php echo number_format($totalAmount, 2); ?></strong>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $booking['booking_status']; ?>">
                                            <?php echo ucfirst($booking['booking_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $booking['payment_status']; ?>">
                                            <?php echo ucfirst($booking['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="btn btn-sm btn-primary" onclick="viewBooking(<?php echo $booking['id']; ?>)">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="updateBookingStatus(<?php echo $booking['id']; ?>)">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            <a href="mailto:<?php echo $booking['customer_email']; ?>" class="btn btn-sm btn-success">
                                                <i class="ri-mail-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Media Library Tab -->
            <div id="images-tab" class="tab-content">
                <div class="image-preview-grid" id="media-library">
                    <?php
                    // Fetch all package images
                    $all_images = $conn->query("
                        SELECT pi.*, p.package_name 
                        FROM package_images pi 
                        JOIN packages p ON pi.package_id = p.id 
                        ORDER BY pi.created_at DESC
                    ");
                    
                    while ($image = $all_images->fetch_assoc()): ?>
                        <div class="image-preview-item">
                            <img src="../upload/<?php echo $image['image_path']; ?>" 
                                 alt="<?php echo htmlspecialchars($image['package_name']); ?>"
                                 onerror="this.src='../../assets/img/bg1.jpg'">
                            <div class="image-overlay">
                                <div style="text-align: center; color: white;">
                                    <div style="font-size: 0.8rem; margin-bottom: 5px;"><?php echo htmlspecialchars($image['package_name']); ?></div>
                                    <?php if ($image['is_primary']): ?>
                                        <div style="font-size: 0.7rem; background: var(--primary-color); padding: 2px 8px; border-radius: 10px; display: inline-block;">
                                            Primary
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Package Modal -->
    <div class="modal" id="package-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2 id="modal-title">Add New Package</h2>
            <form id="package-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="package_id" name="package_id">
                <input type="hidden" name="add_package" id="form-action" value="add_package">
                
                <div class="tabs" style="margin-bottom: 20px;">
                    <div class="tab active" onclick="switchFormTab('basic')">Basic Info</div>
                    <div class="tab" onclick="switchFormTab('details')">Details</div>
                    <div class="tab" onclick="switchFormTab('itinerary')">Itinerary</div>
                    <div class="tab" onclick="switchFormTab('media')">Media</div>
                </div>
                
                <!-- Basic Info Tab -->
                <div id="basic-tab" class="tab-content active">
                    <div class="form-group">
                        <label for="package_name">Package Name *</label>
                        <input type="text" id="package_name" name="package_name" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="package_type">Package Type *</label>
                            <select id="package_type" name="package_type" required>
                                <option value="">Select Type</option>
                                <option value="cultural">Cultural</option>
                                <option value="adventure">Adventure</option>
                                <option value="luxury">Luxury</option>
                                <option value="honeymoon">Honeymoon</option>
                                <option value="family">Family</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="duration_days">Duration (Days) *</label>
                            <input type="number" id="duration_days" name="duration_days" min="1" max="30" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="max_people">Max People *</label>
                            <input type="number" id="max_people" name="max_people" min="1" max="50" required>
                        </div>
                        <div class="form-group">
                            <label for="accommodation_type">Accommodation Type</label>
                            <input type="text" id="accommodation_type" name="accommodation_type" placeholder="e.g., 4 Star Hotels, Luxury Houseboats">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price_per_person">Price per Person (₹) *</label>
                            <input type="number" id="price_per_person" name="price_per_person" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="badge">Badge (Optional)</label>
                            <select id="badge" name="badge">
                                <option value="">No Badge</option>
                                <option value="Bestseller">Bestseller</option>
                                <option value="Popular">Popular</option>
                                <option value="Luxury">Luxury</option>
                                <option value="Adventure">Adventure</option>
                                <option value="Family">Family</option>
                                <option value="Romantic">Romantic</option>
                                <option value="Group">Group</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="rating">Rating (0-5)</label>
                            <input type="number" id="rating" name="rating" step="0.1" min="0" max="5" value="4.9">
                        </div>
                        <div class="form-group">
                            <label for="reviews_count">Reviews Count</label>
                            <input type="number" id="reviews_count" name="reviews_count" min="0" value="128">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="4" required placeholder="Describe your package in detail..."></textarea>
                    </div>
                </div>
                
                <!-- Details Tab -->
                <div id="details-tab" class="tab-content">
                    <!-- Highlights -->
                    <div class="json-section">
                        <h5>Highlights</h5>
                        <div id="highlights-container">
                            <div class="json-item">
                                <input type="text" name="highlight_titles[]" placeholder="Highlight title" required>
                                <input type="text" name="highlight_descriptions[]" placeholder="Highlight description" required>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary add-json-item" onclick="addJsonItem('highlights')">
                            <i class="ri-add-line"></i> Add Highlight
                        </button>
                    </div>

                    <!-- Inclusions -->
                    <div class="json-section">
                        <h5>Inclusions</h5>
                        <div id="inclusions-container">
                            <div class="json-item">
                                <input type="text" name="inclusions[]" placeholder="Included item" required>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary add-json-item" onclick="addJsonItem('inclusions')">
                            <i class="ri-add-line"></i> Add Inclusion
                        </button>
                    </div>

                    <!-- Exclusions -->
                    <div class="json-section">
                        <h5>Exclusions</h5>
                        <div id="exclusions-container">
                            <div class="json-item">
                                <input type="text" name="exclusions[]" placeholder="Excluded item" required>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary add-json-item" onclick="addJsonItem('exclusions')">
                            <i class="ri-add-line"></i> Add Exclusion
                        </button>
                    </div>

                    <!-- FAQs -->
                    <div class="json-section">
                        <h5>Frequently Asked Questions</h5>
                        <div id="faqs-container">
                            <div class="json-item">
                                <input type="text" name="faq_questions[]" placeholder="Question" required>
                                <input type="text" name="faq_answers[]" placeholder="Answer" required>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary add-json-item" onclick="addJsonItem('faqs')">
                            <i class="ri-add-line"></i> Add FAQ
                        </button>
                    </div>
                </div>
                
                <!-- Itinerary Tab -->
                <div id="itinerary-tab" class="tab-content">
                    <div class="itinerary-builder">
                        <div id="itinerary-container">
                            <!-- Days will be added here -->
                        </div>
                        <button type="button" class="btn btn-primary" onclick="addDay()">
                            <i class="ri-add-line"></i> Add Day
                        </button>
                    </div>
                </div>
                
                <!-- Media Tab -->
                <div id="media-tab" class="tab-content">
                    <div class="form-group">
                        <label for="package_images">Package Images</label>
                        <input type="file" id="package_images" name="package_images[]" accept="image/*" multiple>
                        <small style="color: var(--text-secondary);">Select multiple images (first image will be primary)</small>
                        <div id="images-preview" class="image-preview-grid"></div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1">
                                <label for="is_featured">Featured Package</label>
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
                        <i class="ri-save-line"></i> Save Package
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Manage Images Modal -->
    <div class="modal" id="images-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeImagesModal()">&times;</span>
            <h2>Manage Package Images</h2>
            <div id="images-list" style="margin: 20px 0;"></div>
            <form id="upload-images-form" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
                <input type="hidden" id="images_package_id" name="package_id">
                <div class="form-group">
                    <label>Add More Images</label>
                    <input type="file" name="package_images[]" accept="image/*" multiple>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-upload-line"></i> Upload Images
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
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="booking_status">Booking Status *</label>
                        <select id="booking_status" name="booking_status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payment_status">Payment Status *</label>
                        <select id="payment_status" name="payment_status" required>
                            <option value="pending">Pending</option>
                            <option value="partial">Partial</option>
                            <option value="paid">Paid</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
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

    <!-- Statistics Modal -->
    <div class="modal" id="stats-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeStatsModal()">&times;</span>
            <h2>Package Statistics</h2>
            <div id="stats-content">
                <!-- Statistics will be loaded here -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Global variables
        let currentPackageId = null;
        let currentDayCount = 1;
        
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
            document.querySelector(`.tab:nth-child(${tabName === 'packages' ? 1 : tabName === 'bookings' ? 2 : 3})`).classList.add('active');
            document.getElementById(`${tabName}-tab`).classList.add('active');
        }
        
        function switchFormTab(tabName) {
            // Update form tabs
            document.querySelectorAll('#package-modal .tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('#package-modal .tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Activate selected tab
            const tabIndex = ['basic', 'details', 'itinerary', 'media'].indexOf(tabName) + 1;
            document.querySelector(`#package-modal .tab:nth-child(${tabIndex})`).classList.add('active');
            document.getElementById(`${tabName}-tab`).classList.add('active');
        }
        
        // Modal functions
        function openAddPackageModal() {
            document.getElementById('modal-title').textContent = 'Add New Package';
            // Ensure the form uses the add_package action
            const actionInput = document.getElementById('form-action');
            actionInput.name = 'add_package';
            actionInput.value = 'add_package';

            document.getElementById('package-form').reset();
            document.getElementById('package_id').value = '';
            document.getElementById('package-form').action = '';
            
            // Reset form tabs
            switchFormTab('basic');
            
            // Clear JSON editors
            ['highlights', 'inclusions', 'exclusions', 'faqs'].forEach(type => {
                const container = document.getElementById(`${type}-container`);
                container.innerHTML = `
                    <div class="json-item">
                        ${getJsonItemHTML(type)}
                    </div>
                `;
            });
            
            // Clear itinerary
            document.getElementById('itinerary-container').innerHTML = '';
            currentDayCount = 1;
            
            // Clear image preview
            document.getElementById('images-preview').innerHTML = '';
            
            document.getElementById('package-modal').classList.add('active');
        }
        
        function getJsonItemHTML(type) {
            switch(type) {
                case 'highlights':
                    return `<input type="text" name="highlight_titles[]" placeholder="Highlight title" required>
                            <input type="text" name="highlight_descriptions[]" placeholder="Highlight description" required>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>`;
                case 'inclusions':
                case 'exclusions':
                    return `<input type="text" name="${type}[]" placeholder="${type === 'inclusions' ? 'Included' : 'Excluded'} item" required>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>`;
                case 'faqs':
                    return `<input type="text" name="faq_questions[]" placeholder="Question" required>
                            <input type="text" name="faq_answers[]" placeholder="Answer" required>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>`;
            }
        }
        
        // JSON Editor Functions
        function addJsonItem(type) {
            let container, html;
            
            switch(type) {
                case 'highlights':
                    container = document.getElementById('highlights-container');
                    html = `
                        <div class="json-item">
                            <input type="text" name="highlight_titles[]" placeholder="Highlight title">
                            <input type="text" name="highlight_descriptions[]" placeholder="Highlight description">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    `;
                    break;
                    
                case 'inclusions':
                    container = document.getElementById('inclusions-container');
                    html = `
                        <div class="json-item">
                            <input type="text" name="inclusions[]" placeholder="Included item">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    `;
                    break;
                    
                case 'exclusions':
                    container = document.getElementById('exclusions-container');
                    html = `
                        <div class="json-item">
                            <input type="text" name="exclusions[]" placeholder="Excluded item">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    `;
                    break;
                    
                case 'faqs':
                    container = document.getElementById('faqs-container');
                    html = `
                        <div class="json-item">
                            <input type="text" name="faq_questions[]" placeholder="Question">
                            <input type="text" name="faq_answers[]" placeholder="Answer">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    `;
                    break;
            }
            
            container.insertAdjacentHTML('beforeend', html);
        }
        
        function removeJsonItem(button) {
            const container = button.closest('.json-item').parentElement;
            if (container.children.length > 1) {
                button.closest('.json-item').remove();
            }
        }
        
        // Itinerary Builder Functions
        function addDay() {
            const container = document.getElementById('itinerary-container');
            const dayNumber = currentDayCount;
            
            const html = `
                <div class="day-card" data-day="${dayNumber}">
                    <div class="day-header">
                        <div>
                            <span class="day-number">Day ${dayNumber}</span>
                            <input type="hidden" name="day_numbers[]" value="${dayNumber}">
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeDay(${dayNumber})">
                            <i class="ri-delete-bin-line"></i> Remove Day
                        </button>
                    </div>
                    <div class="form-group">
                        <label>Day Title</label>
                        <input type="text" name="day_titles[]" placeholder="e.g., Arrival in Srinagar" required>
                    </div>
                    <div class="form-group">
                        <label>Day Description</label>
                        <textarea name="day_descriptions[]" rows="2" placeholder="Brief description of the day's activities"></textarea>
                    </div>
                    <div class="activities-list" id="activities-${dayNumber}">
                        <div class="activity-item">
                            <input type="time" name="day_activities[${dayNumber}][]" placeholder="Time">
                            <input type="text" name="day_activities_desc[${dayNumber}][]" placeholder="Activity description">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeActivity(this)">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addActivity(${dayNumber})">
                        <i class="ri-add-line"></i> Add Activity
                    </button>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
            currentDayCount++;
        }
        
        function addActivity(dayNumber) {
            const container = document.getElementById(`activities-${dayNumber}`);
            const html = `
                <div class="activity-item">
                    <input type="time" name="day_activities[${dayNumber}][]" placeholder="Time">
                    <input type="text" name="day_activities_desc[${dayNumber}][]" placeholder="Activity description">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeActivity(this)">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }
        
        function removeActivity(button) {
            const container = button.closest('.activities-list');
            if (container.children.length > 1) {
                button.closest('.activity-item').remove();
            }
        }
        
        function removeDay(dayNumber) {
            const dayCard = document.querySelector(`.day-card[data-day="${dayNumber}"]`);
            if (dayCard) {
                dayCard.remove();
            }
        }
        
        // Image Preview
        document.getElementById('package_images').addEventListener('change', function(e) {
            const preview = document.getElementById('images-preview');
            preview.innerHTML = '';
            
            Array.from(e.target.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('div');
                    img.className = 'image-preview-item';
                    img.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div class="image-overlay">
                            <div>Image ${index + 1}</div>
                        </div>
                    `;
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        });
        
        // Edit Package
        function editPackage(packageId) {
            fetch(`../logic/get_package.php?id=${packageId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modal-title').textContent = 'Edit Package';
                    // Switch the hidden action input to update mode so server recognizes update
                    const actionInput = document.getElementById('form-action');
                    actionInput.name = 'update_package';
                    actionInput.value = 'update_package';

                    document.getElementById('package_id').value = data.id;
                    document.getElementById('package_name').value = data.package_name;
                    document.getElementById('package_type').value = data.package_type;
                    document.getElementById('duration_days').value = data.duration_days;
                    document.getElementById('max_people').value = data.max_people;
                    document.getElementById('accommodation_type').value = data.accommodation_type || '';
                    document.getElementById('price_per_person').value = data.price_per_person;
                    document.getElementById('description').value = data.description || '';
                    document.getElementById('badge').value = data.badge || '';
                    document.getElementById('rating').value = data.rating || '4.9';
                    document.getElementById('reviews_count').value = data.reviews_count || '128';
                    document.getElementById('is_featured').checked = data.is_featured == 1;
                    document.getElementById('is_active').checked = data.is_active == 1;
                    
                    // Reset to basic tab
                    switchFormTab('basic');
                    
                    // Populate JSON fields
                    populateJsonFields(data);
                    
                    // Populate itinerary
                    populateItinerary(data.itinerary);
                    
                    // Clear current image preview (manage images separately)
                    document.getElementById('images-preview').innerHTML = '';
                    
                    document.getElementById('package-modal').classList.add('active');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading package details');
                });
        }
        
        function populateJsonFields(data) {
            // Clear existing fields
            ['highlights', 'inclusions', 'exclusions', 'faqs'].forEach(type => {
                document.getElementById(`${type}-container`).innerHTML = '';
            });
            
            // Populate highlights
            if (data.highlights && data.highlights.length > 0) {
                data.highlights.forEach(highlight => {
                    const html = `
                        <div class="json-item">
                            <input type="text" name="highlight_titles[]" value="${highlight.title || ''}" placeholder="Highlight title">
                            <input type="text" name="highlight_descriptions[]" value="${highlight.description || ''}" placeholder="Highlight description">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    `;
                    document.getElementById('highlights-container').insertAdjacentHTML('beforeend', html);
                });
            } else {
                document.getElementById('highlights-container').innerHTML = `
                    <div class="json-item">
                        <input type="text" name="highlight_titles[]" placeholder="Highlight title">
                        <input type="text" name="highlight_descriptions[]" placeholder="Highlight description">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                    </div>
                `;
            }
            
            // Populate inclusions
            if (data.inclusions && data.inclusions.length > 0) {
                data.inclusions.forEach(inclusion => {
                    const html = `
                        <div class="json-item">
                            <input type="text" name="inclusions[]" value="${inclusion}" placeholder="Included item">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    `;
                    document.getElementById('inclusions-container').insertAdjacentHTML('beforeend', html);
                });
            } else {
                document.getElementById('inclusions-container').innerHTML = `
                    <div class="json-item">
                        <input type="text" name="inclusions[]" placeholder="Included item">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                    </div>
                `;
            }
            
            // Populate exclusions
            if (data.exclusions && data.exclusions.length > 0) {
                data.exclusions.forEach(exclusion => {
                    const html = `
                        <div class="json-item">
                            <input type="text" name="exclusions[]" value="${exclusion}" placeholder="Excluded item">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    `;
                    document.getElementById('exclusions-container').insertAdjacentHTML('beforeend', html);
                });
            } else {
                document.getElementById('exclusions-container').innerHTML = `
                    <div class="json-item">
                        <input type="text" name="exclusions[]" placeholder="Excluded item">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                    </div>
                `;
            }
            
            // Populate FAQs
            if (data.faqs && data.faqs.length > 0) {
                data.faqs.forEach(faq => {
                    const html = `
                        <div class="json-item">
                            <input type="text" name="faq_questions[]" value="${faq.question || ''}" placeholder="Question">
                            <input type="text" name="faq_answers[]" value="${faq.answer || ''}" placeholder="Answer">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    `;
                    document.getElementById('faqs-container').insertAdjacentHTML('beforeend', html);
                });
            } else {
                document.getElementById('faqs-container').innerHTML = `
                    <div class="json-item">
                        <input type="text" name="faq_questions[]" placeholder="Question">
                        <input type="text" name="faq_answers[]" placeholder="Answer">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                    </div>
                `;
            }
        }
        
        function populateItinerary(itineraryData) {
            const container = document.getElementById('itinerary-container');
            container.innerHTML = '';
            currentDayCount = 1;
            
            if (itineraryData && itineraryData.length > 0) {
                itineraryData.forEach(day => {
                    const html = `
                        <div class="day-card" data-day="${day.day}">
                            <div class="day-header">
                                <div>
                                    <span class="day-number">Day ${day.day}</span>
                                    <input type="hidden" name="day_numbers[]" value="${day.day}">
                                </div>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeDay(${day.day})">
                                    <i class="ri-delete-bin-line"></i> Remove Day
                                </button>
                            </div>
                            <div class="form-group">
                                <label>Day Title</label>
                                <input type="text" name="day_titles[]" value="${day.title || ''}" placeholder="e.g., Arrival in Srinagar" required>
                            </div>
                            <div class="form-group">
                                <label>Day Description</label>
                                <textarea name="day_descriptions[]" rows="2" placeholder="Brief description of the day's activities">${day.description || ''}</textarea>
                            </div>
                            <div class="activities-list" id="activities-${day.day}">
                                ${day.activities && day.activities.length > 0 ? 
                                    day.activities.map(activity => `
                                        <div class="activity-item">
                                            <input type="time" name="day_activities[${day.day}][]" value="${activity.time || ''}" placeholder="Time">
                                            <input type="text" name="day_activities_desc[${day.day}][]" value="${activity.description || ''}" placeholder="Activity description">
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeActivity(this)">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    `).join('') : 
                                    `<div class="activity-item">
                                        <input type="time" name="day_activities[${day.day}][]" placeholder="Time">
                                        <input type="text" name="day_activities_desc[${day.day}][]" placeholder="Activity description">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeActivity(this)">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>`
                                }
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addActivity(${day.day})">
                                <i class="ri-add-line"></i> Add Activity
                            </button>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                    currentDayCount = Math.max(currentDayCount, day.day + 1);
                });
            } else {
                // Add at least one day
                addDay();
            }
        }
        
        // Delete Package
        function deletePackage(packageId) {
            if (confirm('Are you sure you want to delete this package? All related images and testimonials will also be deleted.')) {
                window.location.href = `?delete=${packageId}`;
            }
        }
        
        // Manage Images
        function manageImages(packageId) {
            document.getElementById('images_package_id').value = packageId;
            currentPackageId = packageId;
            loadPackageImages(packageId);
            document.getElementById('images-modal').classList.add('active');
        }
        
        function loadPackageImages(packageId) {
            fetch(`../logic/get_package_images.php?id=${packageId}`)
                .then(response => response.json())
                .then(images => {
                    let html = '<div class="image-preview-grid">';
                    images.forEach(image => {
                        html += `
                            <div class="image-preview-item">
                                <img src="../../assets/img/${image.image_path}" alt="Package Image">
                                <div class="image-overlay">
                                    <div style="text-align: center;">
                                        <button class="btn btn-sm btn-primary" onclick="setPrimaryImage(${image.id})" style="margin: 5px; ${image.is_primary ? 'display: none;' : ''}">
                                            <i class="ri-star-line"></i> Set Primary
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteImage(${image.id})" style="margin: 5px;">
                                            <i class="ri-delete-bin-line"></i> Delete
                                        </button>
                                        ${image.is_primary ? '<div style="background: var(--primary-color); padding: 5px 10px; border-radius: 10px; font-size: 0.8rem;">Primary Image</div>' : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    document.getElementById('images-list').innerHTML = html;
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
        
        // Booking Management
        function viewBooking(bookingId) {
            fetch(`../logic/get_package_booking.php?id=${bookingId}`)
                .then(response => response.json())
                .then(data => {
                    const details = `
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Booking Information</h3>
                            <p><strong>Booking ID:</strong> ${data.booking_reference}</p>
                            <p><strong>Package:</strong> ${data.package_name}</p>
                            <p><strong>Customer:</strong> ${data.customer_name}</p>
                            <p><strong>Email:</strong> ${data.customer_email}</p>
                            <p><strong>Phone:</strong> ${data.customer_phone}</p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Trip Details</h3>
                            <p><strong>Check-in:</strong> ${new Date(data.checkin_date).toLocaleDateString()}</p>
                            <p><strong>Check-out:</strong> ${new Date(data.checkout_date).toLocaleDateString()}</p>
                            <p><strong>Duration:</strong> ${data.total_days} days</p>
                            <p><strong>Guests:</strong> ${data.number_of_adults} Adults, ${data.number_of_children} Children</p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Payment Details</h3>
                            <p><strong>Total Amount:</strong> ₹${parseFloat(data.total_amount).toFixed(2)}</p>
                            <p><strong>Booking Status:</strong> <span class="status-badge status-${data.booking_status}">${data.booking_status.charAt(0).toUpperCase() + data.booking_status.slice(1)}</span></p>
                            <p><strong>Payment Status:</strong> <span class="status-badge status-${data.payment_status}">${data.payment_status.charAt(0).toUpperCase() + data.payment_status.slice(1)}</span></p>
                        </div>
                        
                        ${data.customer_notes ? `
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Customer Notes</h3>
                            <p>${data.customer_notes}</p>
                        </div>
                        ` : ''}
                        
                        ${data.notes ? `
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 16px; color: var(--primary-color);">Admin Notes</h3>
                            <p>${data.notes}</p>
                        </div>
                        ` : ''}
                        
                        <div style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 24px;">
                            <p><strong>Booked on:</strong> ${new Date(data.booked_at).toLocaleString()}</p>
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
            
            fetch(`../logic/get_package_booking.php?id=${bookingId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('booking_status').value = data.booking_status;
                    document.getElementById('payment_status').value = data.payment_status;
                    document.getElementById('notes').value = data.notes || '';
                    document.getElementById('status-modal').classList.add('active');
                });
        }
        
        // Bulk Actions
        function toggleSelectAll() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.package-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        }
        
        function confirmBulkAction() {
            const selected = document.querySelectorAll('.package-checkbox:checked');
            if (selected.length === 0) {
                alert('Please select at least one package.');
                return false;
            }
            
            const action = document.querySelector('select[name="bulk_action"]').value;
            if (!action) {
                alert('Please select a bulk action.');
                return false;
            }
            
            return confirm(`Are you sure you want to ${action} ${selected.length} package(s)?`);
        }
        
        // Search and Filter
        function searchPackages() {
            const searchTerm = document.getElementById('package-search').value.toLowerCase();
            const rows = document.querySelectorAll('.package-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }
        
        function filterByType() {
            const type = document.getElementById('filter-type').value;
            const rows = document.querySelectorAll('.package-row');
            
            rows.forEach(row => {
                const rowType = row.getAttribute('data-type');
                row.style.display = (!type || rowType === type) ? '' : 'none';
            });
        }
        
        function filterByStatus() {
            const status = document.getElementById('filter-status').value;
            const rows = document.querySelectorAll('.package-row');
            
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                const rowFeatured = row.getAttribute('data-featured');
                let show = false;
                
                switch(status) {
                    case 'active':
                        show = rowStatus === 'active';
                        break;
                    case 'inactive':
                        show = rowStatus === 'inactive';
                        break;
                    case 'featured':
                        show = rowFeatured === 'featured';
                        break;
                    default:
                        show = true;
                }
                
                row.style.display = show ? '' : 'none';
            });
        }
        
        function filterPackages(filter) {
            const rows = document.querySelectorAll('.package-row');
            
            rows.forEach(row => {
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
                    case 'cultural':
                        show = rowType === 'cultural';
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
        
        // Statistics
        function showStats() {
            fetch('../logic/get_package_stats.php')
                .then(response => response.json())
                .then(data => {
                    const html = `
                        <div style="margin-bottom: 30px;">
                            <h3>Package Distribution by Type</h3>
                            <canvas id="typeChart" width="400" height="200"></canvas>
                        </div>
                        <div style="margin-bottom: 30px;">
                            <h3>Monthly Bookings</h3>
                            <canvas id="bookingsChart" width="400" height="200"></canvas>
                        </div>
                        <div>
                            <h3>Revenue by Package</h3>
                            <canvas id="revenueChart" width="400" height="200"></canvas>
                        </div>
                    `;
                    
                    document.getElementById('stats-content').innerHTML = html;
                    document.getElementById('stats-modal').classList.add('active');
                    
                    // Render charts
                    setTimeout(() => {
                        renderCharts(data);
                    }, 100);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('stats-content').innerHTML = '<p>Error loading statistics.</p>';
                });
        }
        
        function renderCharts(data) {
            // Type Distribution Chart
            const typeCtx = document.getElementById('typeChart').getContext('2d');
            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: data.typeLabels || ['Cultural', 'Adventure', 'Luxury', 'Honeymoon', 'Family'],
                    datasets: [{
                        data: data.typeData || [5, 3, 2, 4, 3],
                        backgroundColor: [
                            '#10b981',
                            '#f59e0b',
                            '#8b5cf6',
                            '#ec4899',
                            '#3b82f6'
                        ]
                    }]
                }
            });
            
            // Bookings Chart
            const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
            new Chart(bookingsCtx, {
                type: 'line',
                data: {
                    labels: data.monthLabels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Bookings',
                        data: data.bookingData || [12, 19, 8, 15, 22, 18],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true
                    }]
                }
            });
        }
        
        // Export Packages
        function exportPackages() {
            window.location.href = '../logic/export_packages.php';
        }
        
        // Close modals
        function closeModal() {
            document.getElementById('package-modal').classList.remove('active');
            // Reset hidden action input to add mode to avoid accidental updates
            const actionInput = document.getElementById('form-action');
            if (actionInput) {
                actionInput.name = 'add_package';
                actionInput.value = 'add_package';
            }
        }
        
        function closeImagesModal() {
            document.getElementById('images-modal').classList.remove('active');
        }
        
        function closeBookingModal() {
            document.getElementById('booking-modal').classList.remove('active');
        }
        
        function closeStatusModal() {
            document.getElementById('status-modal').classList.remove('active');
        }
        
        function closeStatsModal() {
            document.getElementById('stats-modal').classList.remove('active');
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
        document.getElementById('package-form').addEventListener('submit', function(e) {
            // Validate required fields
            const requiredFields = ['package_name', 'package_type', 'duration_days', 'max_people', 'price_per_person', 'description'];
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
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields marked with *');
                return false;
            }
            
            // Validate numeric fields
            const price = document.getElementById('price_per_person').value;
            if (price < 0) {
                e.preventDefault();
                alert('Price cannot be negative');
                return false;
            }
            
            const duration = document.getElementById('duration_days').value;
            if (duration < 1 || duration > 30) {
                e.preventDefault();
                alert('Duration must be between 1 and 30 days');
                return false;
            }
            
            const maxPeople = document.getElementById('max_people').value;
            if (maxPeople < 1 || maxPeople > 50) {
                e.preventDefault();
                alert('Maximum people must be between 1 and 50');
                return false;
            }
            
            return true;
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Add initial JSON items
            ['highlights', 'inclusions', 'exclusions', 'faqs'].forEach(type => {
                addJsonItem(type);
            });
            
            // Initialize date pickers
            const today = new Date().toISOString().split('T')[0];
            document.querySelectorAll('input[type="date"]').forEach(input => {
                input.min = today;
            });
        });
    </script>
</body>
</html>