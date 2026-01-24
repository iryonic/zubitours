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

$form_action = $_POST['form_action'] ?? '';

// Add new destination
if ($form_action === 'add_destination') {
    $destination_name = $_POST['destination_name'];
    $region = $_POST['region'];
    $destination_type = $_POST['destination_type'];
    $best_seasons = json_encode($_POST['best_seasons'] ?? []);
    $location = $_POST['location'];
    $short_description = $_POST['short_description'];
    $detailed_description = $_POST['detailed_description'];
    $badge = $_POST['badge'];
    $rating = $_POST['rating'];
    $reviews_count = $_POST['reviews_count'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $duration_days = $_POST['duration_days'] ?? 0;
    $price_per_person = $_POST['price_per_person'] ?? 0;
    $max_people = $_POST['max_people'] ?? 0;
    $accommodation_type = $_POST['accommodation_type'] ?? '';

    // Handle JSON fields
    $inclusions = [];
    $exclusions = [];
    $faqs = [];
    
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
    
    // Handle itinerary
    $itinerary = [];
    if (isset($_POST['day_numbers']) && isset($_POST['day_titles']) && isset($_POST['day_descriptions'])) {
        $day_numbers = $_POST['day_numbers'];
        $day_titles = $_POST['day_titles'];
        $day_descriptions = $_POST['day_descriptions'];
        $day_activities = $_POST['day_activities'] ?? [];
        $day_activities_desc = $_POST['day_activities_desc'] ?? [];
        
        for ($i = 0; $i < count($day_numbers); $i++) {
            $current_day = $day_numbers[$i];
            if (!empty(trim($day_titles[$i]))) {
                $activities_list = [];
                if (isset($day_activities[$current_day])) {
                    $times = $day_activities[$current_day];
                    $descs = $day_activities_desc[$current_day] ?? [];
                    for ($k = 0; $k < count($times); $k++) {
                        if (!empty(trim($times[$k])) || !empty(trim($descs[$k]))) {
                            $activities_list[] = [
                                'time' => $times[$k],
                                'description' => $descs[$k]
                            ];
                        }
                    }
                }
                $itinerary[] = [
                    'day' => $current_day,
                    'title' => $day_titles[$i],
                    'description' => $day_descriptions[$i],
                    'activities' => $activities_list
                ];
            }
        }
    }

    $inclusions_json = json_encode($inclusions);
    $exclusions_json = json_encode($exclusions);
    $faqs_json = json_encode($faqs);
    $itinerary_json = json_encode($itinerary);
    
    $slug = createSlug($destination_name);
    
    $stmt = $conn->prepare("INSERT INTO destinations (destination_name, slug, region, destination_type, duration_days, price_per_person, max_people, accommodation_type, best_seasons, location, short_description, detailed_description, itinerary, inclusions, exclusions, faqs, badge, rating, reviews_count, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiiissssssssssdsii", $destination_name, $slug, $region, $destination_type, $duration_days, $price_per_person, $max_people, $accommodation_type, $best_seasons, $location, $short_description, $detailed_description, $itinerary_json, $inclusions_json, $exclusions_json, $faqs_json, $badge, $rating, $reviews_count, $is_featured, $is_active);
    
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
if ($form_action === 'update_destination') {
    $id = $_POST['destination_id'];
    $destination_name = $_POST['destination_name'];
    $region = $_POST['region'];
    $destination_type = $_POST['destination_type'];
    $best_seasons = json_encode($_POST['best_seasons'] ?? []);
    $location = $_POST['location'];
    $short_description = $_POST['short_description'];
    $detailed_description = $_POST['detailed_description'];
    $badge = $_POST['badge'];
    $rating = $_POST['rating'];
    $reviews_count = $_POST['reviews_count'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $duration_days = $_POST['duration_days'] ?? 0;
    $price_per_person = $_POST['price_per_person'] ?? 0;
    $max_people = $_POST['max_people'] ?? 0;
    $accommodation_type = $_POST['accommodation_type'] ?? '';

    // Handle JSON fields (same as add)
    $inclusions = [];
    $exclusions = [];
    $faqs = [];
    
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
    
    $itinerary = [];
    if (isset($_POST['day_numbers']) && isset($_POST['day_titles']) && isset($_POST['day_descriptions'])) {
        $day_numbers = $_POST['day_numbers'];
        $day_titles = $_POST['day_titles'];
        $day_descriptions = $_POST['day_descriptions'];
        $day_activities = $_POST['day_activities'] ?? [];
        $day_activities_desc = $_POST['day_activities_desc'] ?? [];
        
        for ($i = 0; $i < count($day_numbers); $i++) {
            $current_day = $day_numbers[$i];
            if (!empty(trim($day_titles[$i]))) {
                $activities_list = [];
                if (isset($day_activities[$current_day])) {
                    $times = $day_activities[$current_day];
                    $descs = $day_activities_desc[$current_day] ?? [];
                    for ($k = 0; $k < count($times); $k++) {
                        if (!empty(trim($times[$k])) || !empty(trim($descs[$k]))) {
                            $activities_list[] = [
                                'time' => $times[$k],
                                'description' => $descs[$k]
                            ];
                        }
                    }
                }
                $itinerary[] = [
                    'day' => $current_day,
                    'title' => $day_titles[$i],
                    'description' => $day_descriptions[$i],
                    'activities' => $activities_list
                ];
            }
        }
    }

    $inclusions_json = json_encode($inclusions);
    $exclusions_json = json_encode($exclusions);
    $faqs_json = json_encode($faqs);
    $itinerary_json = json_encode($itinerary);
    $slug = createSlug($destination_name);
    
    $stmt = $conn->prepare("UPDATE destinations SET destination_name = ?, slug = ?, region = ?, destination_type = ?, duration_days = ?, price_per_person = ?, max_people = ?, accommodation_type = ?, best_seasons = ?, location = ?, short_description = ?, detailed_description = ?, itinerary = ?, inclusions = ?, exclusions = ?, faqs = ?, badge = ?, rating = ?, reviews_count = ?, is_featured = ?, is_active = ? WHERE id = ?");
    $stmt->bind_param("ssssiiissssssssssdsiii", $destination_name, $slug, $region, $destination_type, $duration_days, $price_per_person, $max_people, $accommodation_type, $best_seasons, $location, $short_description, $detailed_description, $itinerary_json, $inclusions_json, $exclusions_json, $faqs_json, $badge, $rating, $reviews_count, $is_featured, $is_active, $id);
    
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
$total_inquiries = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE destination_id IS NOT NULL")->fetch_assoc()['count'];
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

        /* Content Modal Styles */
        .content-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid var(--border-color);
        }
        .content-item-info {
            flex: 1;
        }
        .content-item-info h5 {
            margin: 0 0 4px 0;
            color: var(--text-primary);
        }
        .content-item-info p {
            margin: 0;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }
        .content-tab-box {
            display: none;
            padding-top: 20px;
        }
        .content-tab-box.active {
            display: block;
        }
        .add-content-form {
            background: rgba(255,255,255,0.03);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px dashed var(--border-color);
        }
        .main-tab-content {
            display: none;
        }
        .main-tab-content.active {
            display: block;
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

                <div class="stat-card">
                    <div class="stat-icon revenue-icon">
                        <i class="ri-message-2-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_inquiries; ?></h3>
                        <p>Total Inquiries</p>
                    </div>
                </div>
            </div>

            <!-- Main Tabs -->
            <div class="tabs" style="margin-bottom: 30px;">
                <div class="tab main-tab active" data-main-tab="destinations" onclick="switchMainTab('destinations')">
                    Destinations List
                    <span class="tab-badge"><?php echo $total_destinations; ?></span>
                </div>
                <div class="tab main-tab" data-main-tab="inquiries" onclick="switchMainTab('inquiries')">
                    Quick Inquiries
                    <span class="tab-badge"><?php echo $total_inquiries; ?></span>
                </div>
            </div>

            <!-- Destinations List Tab Content -->
            <div id="destinations-main-tab" class="main-tab-content active">
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
                                    
                                    <div class="featured-toggle" onclick="toggleFeatured(<?php echo $destination['id']; ?>, <?php echo $destination['is_featured'] ? 0 : 1; ?>)" style="cursor: pointer;">
                                        <?php if ($destination['is_featured']): ?>
                                            <span class="status-badge status-featured" style="margin-top: 5px; background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid #f59e0b;">
                                                <i class="ri-star-fill"></i> Featured
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge" style="margin-top: 5px; opacity: 0.5;">
                                                <i class="ri-star-line"></i> Not Featured
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="../../public/destination-details.php?id=<?php echo $destination['id']; ?>" class="btn btn-primary btn-sm" target="_blank">
                                            <i class="ri-external-link-line"></i> View
                                        </a>
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

            <!-- Inquiries List Tab Content -->
            <div id="inquiries-main-tab" class="main-tab-content">
                <div class="table-container">
                    <div id="main-inquiries-container">
                        <p style="text-align: center; color: var(--text-secondary); padding: 40px;">Loading inquires...</p>
                    </div>
                </div>
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
                    <div class="tab" data-tab="details" onclick="switchFormTab('details')">Details</div>
                    <div class="tab" data-tab="itinerary" onclick="switchFormTab('itinerary')">Plan</div>
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

                    <div class="form-row">
                        <div class="form-group">
                            <label for="duration_days">Duration (Days) *</label>
                            <input type="number" id="duration_days" name="duration_days" min="0" value="0">
                        </div>
                        <div class="form-group">
                            <label for="price_per_person">Price per Person (₹) *</label>
                            <input type="number" id="price_per_person" name="price_per_person" step="0.01" min="0" value="0">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="max_people">Max People</label>
                            <input type="number" id="max_people" name="max_people" min="0" value="0">
                        </div>
                        <div class="form-group">
                            <label for="accommodation_type">Accommodation Type</label>
                            <input type="text" id="accommodation_type" name="accommodation_type" placeholder="e.g. 3-star Hotels">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="short_description">Short Description *</label>
                        <textarea id="short_description" name="short_description" rows="3" required placeholder="Brief description for cards and listings..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="detailed_description">Detailed Description</label>
                        <textarea id="detailed_description" name="detailed_description" rows="6" placeholder="Full description for the explore page..."></textarea>
                    </div>
                </div>

                <!-- Details Tab -->
                <div id="details-tab" class="tab-content">
                    <style>
                        .json-section { background: var(--bg-hover); padding: 20px; border-radius: 12px; margin-bottom: 20px; }
                        .json-section h5 { margin-bottom: 15px; color: var(--primary-color); }
                        .json-item { display: flex; gap: 10px; margin-bottom: 10px; }
                        .json-item input { flex: 1; }
                        .add-json-item { margin-top: 10px; }
                    </style>
                    <!-- Inclusions -->
                    <div class="json-section">
                        <h5>Inclusions</h5>
                        <div id="inclusions-container">
                            <div class="json-item">
                                <input type="text" name="inclusions[]" placeholder="Included item">
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
                                <input type="text" name="exclusions[]" placeholder="Excluded item">
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
                                <input type="text" name="faq_questions[]" placeholder="Question">
                                <input type="text" name="faq_answers[]" placeholder="Answer">
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
                    <style>
                        .day-card { background: white; border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 20px; }
                        .day-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; }
                        .day-number { font-weight: 700; color: var(--primary-color); }
                        .activity-item { display: flex; gap: 10px; margin-bottom: 10px; background: var(--bg-hover); padding: 10px; border-radius: 8px; }
                        .activity-item input[type="time"] { width: 120px; }
                    </style>
                    <div id="itinerary-container">
                        <!-- Days will be added here -->
                    </div>
                    <button type="button" class="btn btn-primary" onclick="addDay()">
                        <i class="ri-add-line"></i> Add New Day
                    </button>
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

    <!-- Manage Content Modal -->
    <div class="modal" id="content-modal">
        <div class="modal-content" style="max-width: 800px;">
            <span class="close-modal" onclick="closeContentModal()">&times;</span>
            <h2>Manage Destination Content</h2>
            <p id="content-destination-name" style="color: var(--primary-color); font-weight: 600; margin-top: -10px; margin-bottom: 20px;"></p>
            
            <div class="tabs" style="margin-bottom: 20px;">
                <div class="tab active" data-content-tab="highlights" onclick="switchContentTab('highlights')">Highlights</div>
                <div class="tab" data-content-tab="activities" onclick="switchContentTab('activities')">Activities</div>
                <div class="tab" data-content-tab="tips" onclick="switchContentTab('tips')">Travel Tips</div>
                <div class="tab" data-content-tab="nearby" onclick="switchContentTab('nearby')">Nearby</div>
            </div>

            <!-- Highlights Tab -->
            <div id="highlights-tab-box" class="content-tab-box active">
                <div class="add-content-form">
                    <h4>Add New Highlight</h4>
                    <form onsubmit="saveContentItem(event, 'add_highlight')">
                        <input type="hidden" name="destination_id" class="modal-destination-id">
                        <input type="hidden" name="action" value="add_highlight">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" required placeholder="e.g. Scenic Views">
                            </div>
                            <div class="form-group">
                                <label>Icon (RemixIcon)</label>
                                <input type="text" name="icon" placeholder="ri-image-line" value="ri-check-line">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Add Highlight</button>
                    </form>
                </div>
                <div id="highlights-list"></div>
            </div>

            <!-- Activities Tab -->
            <div id="activities-tab-box" class="content-tab-box">
                <div class="add-content-form">
                    <h4>Add New Activity</h4>
                    <form onsubmit="saveContentItem(event, 'add_activity')">
                        <input type="hidden" name="destination_id" class="modal-destination-id">
                        <input type="hidden" name="action" value="add_activity">
                        <div class="form-row">
                            <div class="form-group" style="flex: 2;">
                                <label>Activity Name</label>
                                <input type="text" name="activity_name" required>
                            </div>
                            <div class="form-group">
                                <label>Difficulty</label>
                                <select name="difficulty_level">
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Duration (Hrs)</label>
                                <input type="number" name="duration_hours" step="0.5" value="0">
                            </div>
                            <div class="form-group">
                                <label>Icon</label>
                                <input type="text" name="icon" placeholder="ri-direction-line" value="ri-direction-line text-blue-500">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Add Activity</button>
                    </form>
                </div>
                <div id="activities-list"></div>
            </div>

            <!-- Tips Tab -->
            <div id="tips-tab-box" class="content-tab-box">
                <div class="add-content-form">
                    <h4>Add Travel Tip</h4>
                    <form onsubmit="saveContentItem(event, 'add_tip')">
                        <input type="hidden" name="destination_id" class="modal-destination-id">
                        <input type="hidden" name="action" value="add_tip">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Tip Type</label>
                                <select name="tip_type">
                                    <option value="general">General</option>
                                    <option value="best_time">Best Time</option>
                                    <option value="what_to_pack">What to Pack</option>
                                    <option value="safety">Safety</option>
                                    <option value="transport">Transport</option>
                                    <option value="food">Food</option>
                                </select>
                            </div>
                            <div class="form-group" style="flex: 2;">
                                <label>Title</label>
                                <input type="text" name="title" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Add Tip</button>
                    </form>
                </div>
                <div id="tips-list"></div>
            </div>

            <!-- Nearby Tab -->
            <div id="nearby-tab-box" class="content-tab-box">
                <div class="add-content-form">
                    <h4>Add Nearby Attraction</h4>
                    <form onsubmit="saveContentItem(event, 'add_nearby')">
                        <input type="hidden" name="destination_id" class="modal-destination-id">
                        <input type="hidden" name="action" value="add_nearby">
                        <div class="form-row">
                            <div class="form-group" style="flex: 2;">
                                <label>Attraction Name</label>
                                <input type="text" name="attraction_name" required>
                            </div>
                            <div class="form-group">
                                <label>Distance (KM)</label>
                                <input type="number" name="distance_km" step="0.1" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <textarea name="description" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Add Attraction</button>
                    </form>
                </div>
                <div id="nearby-list"></div>
            </div>
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

        function switchMainTab(tabName) {
            // Remove active from all tabs/contents
            document.querySelectorAll('.main-tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.main-tab-content').forEach(content => content.classList.remove('active'));

            // Set active
            const tabEl = document.querySelector(`.main-tab[data-main-tab="${tabName}"]`);
            if (tabEl) tabEl.classList.add('active');

            const contentEl = document.getElementById(`${tabName}-main-tab`);
            if (contentEl) contentEl.classList.add('active');

            // Load content if needed
            if (tabName === 'inquiries') {
                loadInquiries();
            }
        }
        
        // Modal functions
        function openAddDestinationModal() {
            document.getElementById('modal-title').textContent = 'Add New Destination';
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

            // Reset dynamic fields
            populateJsonFields({inclusions: [], exclusions: [], faqs: []});
            populateItinerary([]);
            
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
                    document.getElementById('form_action').value = 'update_destination';
                    
                    document.getElementById('destination_id').value = data.id;
                    document.getElementById('destination_name').value = data.destination_name;
                    document.getElementById('region').value = data.region;
                    document.getElementById('destination_type').value = data.destination_type;
                    document.getElementById('location').value = data.location;
                    
                    document.getElementById('short_description').value = data.short_description;
                    document.getElementById('detailed_description').value = data.detailed_description || '';
                    document.getElementById('badge').value = data.badge || '';
                    document.getElementById('rating').value = data.rating || '4.5';
                    document.getElementById('reviews_count').value = data.reviews_count || '100';
                    document.getElementById('is_featured').checked = data.is_featured == 1;
                    document.getElementById('is_active').checked = data.is_active == 1;

                    document.getElementById('duration_days').value = data.duration_days || 0;
                    document.getElementById('price_per_person').value = data.price_per_person || 0;
                    document.getElementById('max_people').value = data.max_people || 0;
                    document.getElementById('accommodation_type').value = data.accommodation_type || '';

                    // Populate Details and Itinerary
                    populateJsonFields(data);
                    populateItinerary(data.itinerary);
                    
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
        
        async function loadInquiries() {
            const container = document.getElementById('main-inquiries-container');
            if (!container) return;

            try {
                const response = await fetch(`../logic/get_destination_inquiries.php`);
                const inquiries = await response.json();

                if (inquiries.error) {
                    container.innerHTML = `<p style="text-align: center; color: var(--error-color); padding: 20px;">Error: ${inquiries.error}</p>`;
                    return;
                }

                if (inquiries.length === 0) {
                    container.innerHTML = `<p style="text-align: center; color: var(--text-secondary); padding: 40px;">No inquiries found.</p>`;
                    return;
                }

                let html = `
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" id="selectAllInquiries" onclick="toggleAllInquiries(this)"></th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Destination</th>
                                    <th>Source</th>
                                    <th>Travel Details</th>
                                    <th>Message</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                inquiries.forEach(inq => {
                    const date = new Date(inq.created_at).toLocaleDateString();
                    html += `
                        <tr>
                            <td><input type="checkbox" class="inquiry-checkbox" value="${inq.id}"></td>
                            <td>${date}</td>
                            <td>
                                <strong>${inq.name}</strong><br>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);">${inq.email}</span><br>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);">${inq.phone}</span>
                            </td>
                            <td>
                                <span class="status-badge" style="background: rgba(37, 99, 235, 0.1); color: var(--primary-color);">
                                    ${inq.destination_name}
                                </span>
                            </td>
                            <td>
                                <small style="color: var(--text-secondary);">${inq.source || 'Website'}</small>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem;">
                                    <strong>Pax:</strong> ${inq.adults}A, ${inq.children}C<br>
                                    <strong>Date:</strong> ${inq.travel_date || 'N/A'}
                                </span>
                            </td>
                            <td>
                                <div style="max-width: 250px; font-size: 0.85rem; color: var(--text-secondary);">
                                    ${inq.message}
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="deleteInquiry(${inq.id})" title="Delete">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                html += `</tbody></table></div>`;
                
                // Add bulk action button
                const bulkBtnHtml = `
                    <div style="margin-top: 15px; display: flex; align-items: center; gap: 10px;">
                        <button class="btn btn-danger btn-sm" onclick="bulkDeleteInquiries()">
                            <i class="ri-delete-bin-line"></i> Delete Selected
                        </button>
                        <span id="selected-count" style="font-size: 0.85rem; color: var(--text-secondary);">0 selected</span>
                    </div>
                `;
                
                container.innerHTML = html + bulkBtnHtml;

                // Add event listeners for checkboxes to update count
                document.querySelectorAll('.inquiry-checkbox').forEach(cb => {
                    cb.addEventListener('change', updateSelectedInquiryCount);
                });

            } catch (error) {
                console.error('Error loading inquiries:', error);
                container.innerHTML = `<p style="text-align: center; color: var(--error-color); padding: 20px;">Failed to load inquiries.</p>`;
            }
        }

        function toggleAllInquiries(source) {
            const checkboxes = document.querySelectorAll('.inquiry-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
            updateSelectedInquiryCount();
        }

        function updateSelectedInquiryCount() {
            const count = document.querySelectorAll('.inquiry-checkbox:checked').length;
            const countDisplay = document.getElementById('selected-count');
            if (countDisplay) {
                countDisplay.textContent = `${count} selected`;
            }
        }

        async function deleteInquiry(id) {
            if (!confirm('Are you sure you want to delete this inquiry?')) return;
            
            try {
                const response = await fetch('../logic/delete_inquiry.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const result = await response.json();
                if (result.success) {
                    loadInquiries();
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                console.error('Error deleting inquiry:', error);
                alert('Failed to delete inquiry');
            }
        }

        async function bulkDeleteInquiries() {
            const selected = Array.from(document.querySelectorAll('.inquiry-checkbox:checked')).map(cb => cb.value);
            if (selected.length === 0) {
                alert('Please select at least one inquiry to delete.');
                return;
            }
            
            if (!confirm(`Are you sure you want to delete ${selected.length} selected inquiries?`)) return;
            
            try {
                const response = await fetch('../logic/delete_inquiry.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids: selected })
                });
                const result = await response.json();
                if (result.success) {
                    loadInquiries();
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                console.error('Error in bulk delete:', error);
                alert('Failed to delete inquiries');
            }
        }

        // Helper functions for dynamic fields
        let currentDayCount = 1;

        function addJsonItem(type) {
            const container = document.getElementById(`${type}-container`);
            let html = '';
            if (type === 'faqs') {
                html = `
                    <div class="json-item">
                        <input type="text" name="faq_questions[]" placeholder="Question">
                        <input type="text" name="faq_answers[]" placeholder="Answer">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                    </div>
                `;
            } else {
                html = `
                    <div class="json-item">
                        <input type="text" name="${type}[]" placeholder="${type.charAt(0).toUpperCase() + type.slice(1)} item">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                    </div>
                `;
            }
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeJsonItem(btn) {
            btn.parentElement.remove();
        }

        function populateJsonFields(data) {
            ['inclusions', 'exclusions', 'faqs'].forEach(type => {
                const container = document.getElementById(`${type}-container`);
                container.innerHTML = '';
                const items = data[type];
                if (items && items.length > 0) {
                    items.forEach(item => {
                        let html = '';
                        if (type === 'faqs') {
                            html = `
                                <div class="json-item">
                                    <input type="text" name="faq_questions[]" value="${item.question || ''}" placeholder="Question">
                                    <input type="text" name="faq_answers[]" value="${item.answer || ''}" placeholder="Answer">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                                </div>
                            `;
                        } else {
                            html = `
                                <div class="json-item">
                                    <input type="text" name="${type}[]" value="${item}" placeholder="${type.charAt(0).toUpperCase() + type.slice(1)} item">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeJsonItem(this)"><i class="ri-delete-bin-line"></i></button>
                                </div>
                            `;
                        }
                        container.insertAdjacentHTML('beforeend', html);
                    });
                } else {
                    addJsonItem(type);
                }
            });
        }

        function addDay() {
            const container = document.getElementById('itinerary-container');
            const day = currentDayCount++;
            const html = `
                <div class="day-card" data-day="${day}">
                    <div class="day-header">
                        <div>
                            <span class="day-number">Day ${day}</span>
                            <input type="hidden" name="day_numbers[]" value="${day}">
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeDay(${day})">
                            <i class="ri-delete-bin-line"></i> Remove Day
                        </button>
                    </div>
                    <div class="form-group">
                        <label>Day Title</label>
                        <input type="text" name="day_titles[]" placeholder="e.g. Arrival in Srinagar" required>
                    </div>
                    <div class="form-group">
                        <label>Day Description</label>
                        <textarea name="day_descriptions[]" rows="2" placeholder="Brief description..."></textarea>
                    </div>
                    <div class="activities-list" id="activities-${day}">
                        <div class="activity-item">
                            <input type="time" name="day_activities[${day}][]">
                            <input type="text" name="day_activities_desc[${day}][]" placeholder="Activity description">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeActivity(this)"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addActivity(${day})">
                        <i class="ri-add-line"></i> Add Activity
                    </button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeDay(day) {
            const card = document.querySelector(`.day-card[data-day="${day}"]`);
            card.remove();
            // Re-order days? Maybe not strictly necessary for now as they are sent as arrays
        }

        function addActivity(day) {
            const container = document.getElementById(`activities-${day}`);
            const html = `
                <div class="activity-item">
                    <input type="time" name="day_activities[${day}][]">
                    <input type="text" name="day_activities_desc[${day}][]" placeholder="Activity description">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeActivity(this)"><i class="ri-delete-bin-line"></i></button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeActivity(btn) {
            btn.parentElement.remove();
        }

        function populateItinerary(itineraryData) {
            const container = document.getElementById('itinerary-container');
            container.innerHTML = '';
            currentDayCount = 1;
            
            if (itineraryData && itineraryData.length > 0) {
                itineraryData.forEach(day => {
                    const d = day.day;
                    const html = `
                        <div class="day-card" data-day="${d}">
                            <div class="day-header">
                                <div>
                                    <span class="day-number">Day ${d}</span>
                                    <input type="hidden" name="day_numbers[]" value="${d}">
                                </div>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeDay(${d})">
                                    <i class="ri-delete-bin-line"></i> Remove Day
                                </button>
                            </div>
                            <div class="form-group">
                                <label>Day Title</label>
                                <input type="text" name="day_titles[]" value="${day.title || ''}" placeholder="e.g. Arrival in Srinagar" required>
                            </div>
                            <div class="form-group">
                                <label>Day Description</label>
                                <textarea name="day_descriptions[]" rows="2" placeholder="Brief description...">${day.description || ''}</textarea>
                            </div>
                            <div class="activities-list" id="activities-${d}">
                                ${day.activities && day.activities.length > 0 ? 
                                    day.activities.map(activity => `
                                        <div class="activity-item">
                                            <input type="time" name="day_activities[${d}][]" value="${activity.time || ''}">
                                            <input type="text" name="day_activities_desc[${d}][]" value="${activity.description || ''}" placeholder="Activity description">
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeActivity(this)"><i class="ri-delete-bin-line"></i></button>
                                        </div>
                                    `).join('') : 
                                    `<div class="activity-item">
                                        <input type="time" name="day_activities[${d}][]">
                                        <input type="text" name="day_activities_desc[${d}][]" placeholder="Activity description">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeActivity(this)"><i class="ri-delete-bin-line"></i></button>
                                    </div>`
                                }
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addActivity(${d})">
                                <i class="ri-add-line"></i> Add Activity
                            </button>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                    currentDayCount = Math.max(currentDayCount, parseInt(d, 10) + 1);
                });
            } else {
                addDay();
            }
        }
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

        // Manage Content Logic
        function manageDestinationContent(destinationId) {
            currentDestinationId = destinationId;
            document.querySelectorAll('.modal-destination-id').forEach(el => el.value = destinationId);
            
            // Set name in modal
            const row = document.querySelector(`button[onclick*="manageDestinationContent(${destinationId})"]`).closest('tr');
            const name = row.querySelector('h4').textContent;
            document.getElementById('content-destination-name').textContent = "Destination: " + name;

            loadDestinationContent(destinationId);
            document.getElementById('content-modal').classList.add('active');
        }

        function switchContentTab(tabName) {
            document.querySelectorAll('#content-modal .tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.content-tab-box').forEach(b => b.classList.remove('active'));

            const tab = document.querySelector(`[data-content-tab="${tabName}"]`);
            if (tab) tab.classList.add('active');
            
            const box = document.getElementById(`${tabName}-tab-box`);
            if (box) box.classList.add('active');
        }

        function loadDestinationContent(destinationId) {
            fetch(`../logic/get_destination_content.php?id=${destinationId}`)
                .then(r => r.json())
                .then(data => {
                    renderHighlights(data.highlights);
                    renderActivities(data.activities);
                    renderTips(data.tips);
                    renderNearby(data.nearby);
                });
        }

        function renderHighlights(items) {
            const list = document.getElementById('highlights-list');
            list.innerHTML = items.length ? items.map(item => `
                <div class="content-item">
                    <div class="content-item-info">
                        <h5><i class="${item.icon}"></i> ${item.title}</h5>
                        <p>${item.description}</p>
                    </div>
                    <button class="btn btn-danger btn-sm" onclick="deleteContentItem(${item.id}, 'delete_highlight')">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            `).join('') : '<p style="text-align:center; opacity:0.6;">No highlights added yet.</p>';
        }

        function renderActivities(items) {
            const list = document.getElementById('activities-list');
            list.innerHTML = items.length ? items.map(item => `
                <div class="content-item">
                    <div class="content-item-info">
                        <h5><i class="${item.icon || 'ri-direction-line'}"></i> ${item.activity_name} (${item.difficulty_level})</h5>
                        <p>${item.description} - ${item.duration_hours}h</p>
                    </div>
                    <button class="btn btn-danger btn-sm" onclick="deleteContentItem(${item.id}, 'delete_activity')">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            `).join('') : '<p style="text-align:center; opacity:0.6;">No activities added yet.</p>';
        }

        function renderTips(items) {
            const list = document.getElementById('tips-list');
            list.innerHTML = items.length ? items.map(item => `
                <div class="content-item">
                    <div class="content-item-info">
                        <h5>[${item.tip_type.toUpperCase()}] ${item.title}</h5>
                        <p>${item.description}</p>
                    </div>
                    <button class="btn btn-danger btn-sm" onclick="deleteContentItem(${item.id}, 'delete_tip')">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            `).join('') : '<p style="text-align:center; opacity:0.6;">No travel tips added yet.</p>';
        }

        function renderNearby(items) {
            const list = document.getElementById('nearby-list');
            list.innerHTML = items.length ? items.map(item => `
                <div class="content-item">
                    <div class="content-item-info">
                        <h5>${item.attraction_name} (${item.distance_km} KM)</h5>
                        <p>${item.description || 'No description provided.'}</p>
                    </div>
                    <button class="btn btn-danger btn-sm" onclick="deleteContentItem(${item.id}, 'delete_nearby')">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            `).join('') : '<p style="text-align:center; opacity:0.6;">No nearby attractions added yet.</p>';
        }

        function saveContentItem(e, action) {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            fetch('../logic/manage_destination_content.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    e.target.reset();
                    loadDestinationContent(currentDestinationId);
                } else {
                    alert(data.error);
                }
            });
        }

        function deleteContentItem(id, action) {
            if (!confirm('Are you sure you want to delete this item?')) return;
            
            const formData = new FormData();
            formData.append('action', action);
            formData.append('id', id);
            formData.append('destination_id', currentDestinationId);

            fetch('../logic/manage_destination_content.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    loadDestinationContent(currentDestinationId);
                } else {
                    alert(data.error);
                }
            });
        }

        function closeContentModal() {
            document.getElementById('content-modal').classList.remove('active');
        }

        // Close modals when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });
        
        // Feature Toggle
        function toggleFeatured(id, status) {
            const formData = new FormData();
            formData.append('destination_id', id);
            formData.append('is_featured', status);

            fetch('../logic/toggle_featured.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error);
                }
            });
        }

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