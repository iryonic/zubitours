<?php
session_start();
require_once '../includes/connection.php';

// Check if user is admin (uncomment when ready)
// if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
//     header('Location: ../login.php');
//     exit();
// }

// Handle CRUD operations
$message = '';
$message_type = '';

// Handle message status update
if (isset($_POST['update_message_status'])) {
    $message_id = $_POST['message_id'];
    $status = $_POST['status'];
    $admin_notes = $_POST['admin_notes'] ?? '';
    
    $stmt = $conn->prepare("UPDATE contact_messages SET status = ?, admin_notes = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $admin_notes, $message_id);
    
    if ($stmt->execute()) {
        $message = "Message status updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating message: " . $conn->error;
        $message_type = "error";
    }
}

// Handle reply to message (simulated - in production would send email)
if (isset($_POST['send_reply'])) {
    $message_id = $_POST['message_id'];
    $reply_subject = $_POST['reply_subject'];
    $reply_message = $_POST['reply_message'];
    
    // Here you would typically send an email
    // For now, just update status and add note
    $stmt = $conn->prepare("UPDATE contact_messages SET status = 'replied', admin_notes = CONCAT(COALESCE(admin_notes, ''), '\n\nReplied on " . date('Y-m-d H:i:s') . "') WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    
    if ($stmt->execute()) {
        $message = "Reply sent successfully!";
        $message_type = "success";
    } else {
        $message = "Error sending reply: " . $conn->error;
        $message_type = "error";
    }
}

// Handle FAQ operations
if (isset($_POST['add_faq'])) {
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $category = $_POST['category'];
    $sort_order = $_POST['sort_order'] ?? 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $stmt = $conn->prepare("INSERT INTO faqs (question, answer, category, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $question, $answer, $category, $sort_order, $is_active);
    
    if ($stmt->execute()) {
        $message = "FAQ added successfully!";
        $message_type = "success";
    } else {
        $message = "Error adding FAQ: " . $conn->error;
        $message_type = "error";
    }
}

if (isset($_POST['update_faq'])) {
    $faq_id = $_POST['faq_id'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $category = $_POST['category'];
    $sort_order = $_POST['sort_order'] ?? 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $stmt = $conn->prepare("UPDATE faqs SET question = ?, answer = ?, category = ?, sort_order = ?, is_active = ? WHERE id = ?");
    $stmt->bind_param("sssiii", $question, $answer, $category, $sort_order, $is_active, $faq_id);
    
    if ($stmt->execute()) {
        $message = "FAQ updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating FAQ: " . $conn->error;
        $message_type = "error";
    }
}

if (isset($_GET['delete_faq'])) {
    $faq_id = $_GET['delete_faq'];
    
    if ($conn->query("DELETE FROM faqs WHERE id = $faq_id")) {
        $message = "FAQ deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting FAQ: " . $conn->error;
        $message_type = "error";
    }
}

// Handle contact settings update
if (isset($_POST['update_settings'])) {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $conn->prepare("UPDATE contact_settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->bind_param("ss", $value, $key);
        $stmt->execute();
    }
    
    $message = "Settings updated successfully!";
    $message_type = "success";
}

// Fetch all messages
$messages_query = "
    SELECT cm.*, 
           CASE 
               WHEN DATEDIFF(NOW(), cm.created_at) = 0 THEN 'Today'
               WHEN DATEDIFF(NOW(), cm.created_at) = 1 THEN 'Yesterday'
               ELSE CONCAT(DATEDIFF(NOW(), cm.created_at), ' days ago')
           END as time_ago
    FROM contact_messages cm 
    ORDER BY 
        CASE cm.status 
            WHEN 'new' THEN 1
            WHEN 'read' THEN 2
            WHEN 'replied' THEN 3
            ELSE 4
        END,
        cm.created_at DESC
";
$messages = $conn->query($messages_query);

// Fetch all FAQs
$faqs = $conn->query("SELECT * FROM faqs ORDER BY sort_order ASC, created_at DESC");

// Fetch contact settings
$settings_result = $conn->query("SELECT * FROM contact_settings ORDER BY category, sort_order");
$settings = [];
while ($row = $settings_result->fetch_assoc()) {
    $settings[$row['category']][$row['setting_key']] = $row;
}

// Fetch FAQ categories from settings
$faq_categories = [];
if (isset($settings['faq_categories'])) {
    foreach ($settings['faq_categories'] as $setting) {
        $faq_categories[] = $setting['setting_value'];
    }
}

// Get stats
$total_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages")->fetch_assoc()['count'];
$new_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'new'")->fetch_assoc()['count'];
$total_faqs = $conn->query("SELECT COUNT(*) as count FROM faqs")->fetch_assoc()['count'];
$active_faqs = $conn->query("SELECT COUNT(*) as count FROM faqs WHERE is_active = 1")->fetch_assoc()['count'];

// Get message stats by subject
$message_stats = $conn->query("
    SELECT subject, COUNT(*) as count 
    FROM contact_messages 
    GROUP BY subject 
    ORDER BY count DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contact - Zubi Tours Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <style>
        /* Reuse the same CSS variables and styles from manage-car-rentals.php */
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

        /* Sidebar Styles (same as before) */
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

        .content {
            padding: 32px;
        }

        /* Custom styles for contact management */
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
            flex-wrap: wrap;
        }

        .tab {
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            white-space: nowrap;
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

        .messages-icon {
            background: rgba(37, 99, 235, 0.15);
            color: var(--primary-color);
        }

        .new-messages-icon {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning-color);
        }

        .faq-icon {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
        }

        .active-faq-icon {
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

        /* Message List */
        .messages-container {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        .message-list {
            list-style: none;
        }

        .message-item {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
            cursor: pointer;
            position: relative;
        }

        .message-item:hover {
            background: var(--bg-primary);
            border-radius: 8px;
        }

        .message-item.unread {
            background: rgba(37, 99, 235, 0.05);
            border-left: 4px solid var(--primary-color);
        }

        .message-item.read {
            opacity: 0.8;
        }

        .message-item:last-child {
            border-bottom: none;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .message-sender {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sender-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .sender-info h4 {
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .sender-email {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .message-meta {
            text-align: right;
        }

        .message-time {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .message-subject {
            background: var(--bg-primary);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 12px;
        }

        .message-preview {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .message-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-new {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning-color);
        }

        .status-read {
            background: rgba(37, 99, 235, 0.15);
            color: var(--primary-color);
        }

        .status-replied {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
        }

        .status-archived {
            background: rgba(107, 114, 128, 0.15);
            color: #6b7280;
        }

        /* FAQ List */
        .faq-container {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        .faq-list {
            list-style: none;
        }

        .faq-item {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .faq-item:hover {
            background: var(--bg-primary);
            border-radius: 8px;
        }

        .faq-item:last-child {
            border-bottom: none;
        }

        .faq-question {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-answer {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .faq-category {
            background: var(--bg-primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: var(--text-secondary);
            display: inline-block;
            margin-bottom: 12px;
        }

        .faq-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        /* Settings Form */
        .settings-container {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 32px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
        }

        .settings-category {
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
        }

        .settings-category:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .settings-category h3 {
            font-size: 1.2rem;
            margin-bottom: 24px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .settings-category h3 i {
            color: var(--primary-color);
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .setting-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .setting-item label {
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .setting-item input,
        .setting-item select,
        .setting-item textarea {
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: var(--transition);
            width: 100%;
        }

        .setting-item textarea {
            min-height: 100px;
            resize: vertical;
        }

        .setting-item input:focus,
        .setting-item select:focus,
        .setting-item textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .setting-hint {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-top: 4px;
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
            max-width: 800px;
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

        .modal-header {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-header h2 {
            font-size: 1.5rem;
            color: var(--text-primary);
        }

        .modal-body {
            margin-bottom: 24px;
        }

        .message-details {
            background: var(--bg-primary);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .message-details-item {
            margin-bottom: 12px;
            display: flex;
        }

        .message-details-item strong {
            min-width: 120px;
            color: var(--text-primary);
        }

        .message-details-item span {
            color: var(--text-secondary);
            flex: 1;
        }

        .message-content {
            background: var(--bg-primary);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .message-content p {
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .admin-notes {
            background: rgba(245, 158, 11, 0.1);
            border-left: 4px solid var(--warning-color);
            padding: 16px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .admin-notes h4 {
            color: var(--warning-color);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .admin-notes p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            white-space: pre-wrap;
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

        /* Stats Chart */
        .stats-chart {
            background: var(--card-bg);
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            margin-bottom: 32px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .chart-container {
            height: 300px;
            display: flex;
            align-items: flex-end;
            gap: 20px;
            padding: 20px;
            background: var(--bg-primary);
            border-radius: 12px;
        }

        .chart-bar {
            flex: 1;
            background: var(--primary-color);
            border-radius: 8px 8px 0 0;
            position: relative;
            transition: var(--transition);
            cursor: pointer;
        }

        .chart-bar:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .chart-bar-label {
            position: absolute;
            bottom: -30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-secondary);
            transform: rotate(-45deg);
            transform-origin: left top;
            white-space: nowrap;
        }

        .chart-bar-value {
            position: absolute;
            top: -30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                flex-direction: column;
                height: auto;
                gap: 30px;
            }
            
            .chart-bar {
                width: 100%;
                height: 40px !important;
            }
            
            .chart-bar-label {
                bottom: auto;
                top: 100%;
                left: 0;
                transform: none;
                padding-top: 8px;
            }
            
            .chart-bar-value {
                top: 50%;
                transform: translateY(-50%);
                left: 20px;
                text-align: left;
            }
        }

        @media (max-width: 768px) {
            .message-header {
                flex-direction: column;
                gap: 12px;
            }
            
            .message-meta {
                text-align: left;
                width: 100%;
            }
            
            .tabs {
                overflow-x: auto;
                padding-bottom: 8px;
            }
            
            .tab {
                padding: 10px 16px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .section-header {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .message-actions {
                flex-wrap: wrap;
            }
            
            .modal-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
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
                <a href="./manage-car-rentals.php" class="menu-item">
                    <i class="ri-car-line"></i>
                    <span>Car Rentals</span>
                </a>
                <a href="./manage-contacts.php" class="menu-item active">
                    <i class="ri-mail-line"></i>
                    <span>Contact</span>
                    <?php if ($new_messages > 0): ?>
                        <span class="menu-badge"><?php echo $new_messages; ?></span>
                    <?php endif; ?>
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
                <h1 class="section-title">Manage Contact</h1>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon messages-icon">
                        <i class="ri-mail-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_messages; ?></h3>
                        <p>Total Messages</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon new-messages-icon">
                        <i class="ri-mail-unread-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $new_messages; ?></h3>
                        <p>New Messages</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon faq-icon">
                        <i class="ri-question-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_faqs; ?></h3>
                        <p>Total FAQs</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon active-faq-icon">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $active_faqs; ?></h3>
                        <p>Active FAQs</p>
                    </div>
                </div>
            </div>

            <!-- Message Stats Chart
            <?php if ($message_stats->num_rows > 0): ?>
            <div class="stats-chart">
                <div class="chart-header">
                    <div class="chart-title">Messages by Subject</div>
                </div>
                <div class="chart-container" id="message-chart">
                    <?php 
                    $max_count = 0;
                    $stats_data = [];
                    while ($stat = $message_stats->fetch_assoc()) {
                        $stats_data[] = $stat;
                        if ($stat['count'] > $max_count) {
                            $max_count = $stat['count'];
                        }
                    }
                    ?>
                    <?php foreach ($stats_data as $stat): ?>
                        <?php 
                        $height = $max_count > 0 ? ($stat['count'] / $max_count * 100) : 0;
                        $subject_labels = [
                            'general' => 'General',
                            'booking' => 'Booking',
                            'custom' => 'Custom',
                            'feedback' => 'Feedback',
                            'complaint' => 'Complaint',
                            'other' => 'Other'
                        ];
                        ?>
                        <div class="chart-bar" style="height: <?php echo $height; ?>%; background: <?php 
                            echo $stat['subject'] == 'general' ? 'var(--primary-color)' : 
                                 ($stat['subject'] == 'booking' ? 'var(--success-color)' : 
                                 ($stat['subject'] == 'custom' ? 'var(--warning-color)' : 
                                 ($stat['subject'] == 'feedback' ? '#8b5cf6' : 
                                 ($stat['subject'] == 'complaint' ? 'var(--error-color)' : '#6b7280')))); ?>;" 
                             title="<?php echo $subject_labels[$stat['subject']] . ': ' . $stat['count'] . ' messages'; ?>">
                            <div class="chart-bar-value"><?php echo $stat['count']; ?></div>
                            <div class="chart-bar-label"><?php echo $subject_labels[$stat['subject']]; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?> -->

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" onclick="switchTab('messages')">Messages (<?php echo $new_messages; ?> new)</div>
                <div class="tab" onclick="switchTab('faqs')">FAQs</div>
                <div class="tab" onclick="switchTab('settings')">Contact Settings</div>
            </div>

            <!-- Messages Tab -->
            <div id="messages-tab" class="tab-content active">
                <div class="messages-container">
                    <?php if ($messages->num_rows > 0): ?>
                        <ul class="message-list">
                            <?php while ($msg = $messages->fetch_assoc()): ?>
                                <li class="message-item <?php echo $msg['status']; ?> <?php echo $msg['status'] == 'new' ? 'unread' : 'read'; ?>" 
                                    onclick="viewMessage(<?php echo $msg['id']; ?>)">
                                    <div class="message-header">
                                        <div class="message-sender">
                                            <div class="sender-avatar">
                                                <?php echo strtoupper(substr($msg['name'], 0, 1)); ?>
                                            </div>
                                            <div class="sender-info">
                                                <h4><?php echo htmlspecialchars($msg['name']); ?></h4>
                                                <div class="sender-email"><?php echo $msg['email']; ?></div>
                                            </div>
                                        </div>
                                        <div class="message-meta">
                                            <div class="message-time"><?php echo $msg['time_ago']; ?></div>
                                            <span class="status-badge status-<?php echo $msg['status']; ?>">
                                                <?php echo ucfirst($msg['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="message-subject">
                                        <?php 
                                        $subject_labels = [
                                            'general' => 'General Inquiry',
                                            'booking' => 'Booking Information',
                                            'custom' => 'Custom Package',
                                            'feedback' => 'Feedback',
                                            'complaint' => 'Complaint',
                                            'other' => 'Other'
                                        ];
                                        echo $subject_labels[$msg['subject']] ?? 'General Inquiry';
                                        ?>
                                    </div>
                                    <div class="message-preview">
                                        <?php echo htmlspecialchars(substr($msg['message'], 0, 150)); ?>...
                                    </div>
                                  
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div style="text-align: center; padding: 60px; color: var(--text-secondary);">
                            <i class="ri-inbox-line" style="font-size: 3rem; margin-bottom: 20px; display: block;"></i>
                            <h3>No messages yet</h3>
                            <p>All contact messages will appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- FAQs Tab -->
            <div id="faqs-tab" class="tab-content">
                <div class="section-header" style="margin-bottom: 24px;">
                    <h3 style="font-size: 1.3rem;">Frequently Asked Questions</h3>
                    <button class="btn btn-primary" onclick="openAddFAQModal()">
                        <i class="ri-add-line"></i> Add FAQ
                    </button>
                </div>
                
                <div class="faq-container">
                    <?php if ($faqs->num_rows > 0): ?>
                        <ul class="faq-list">
                            <?php while ($faq = $faqs->fetch_assoc()): ?>
                                <li class="faq-item">
                                    <div class="faq-question">
                                        <?php echo htmlspecialchars($faq['question']); ?>
                                        <?php if (!$faq['is_active']): ?>
                                            <span style="color: var(--text-secondary); font-size: 0.85rem;">(Inactive)</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="faq-category"><?php echo htmlspecialchars($faq['category']); ?></div>
                                    <div class="faq-answer">
                                        <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                                    </div>
                                    <div class="faq-actions">
                                        <button class="btn btn-sm btn-primary" onclick="editFAQ(<?php echo $faq['id']; ?>)">
                                            <i class="ri-edit-line"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteFAQ(<?php echo $faq['id']; ?>)">
                                            <i class="ri-delete-bin-line"></i> Delete
                                        </button>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div style="text-align: center; padding: 60px; color: var(--text-secondary);">
                            <i class="ri-question-line" style="font-size: 3rem; margin-bottom: 20px; display: block;"></i>
                            <h3>No FAQs yet</h3>
                            <p>Add some frequently asked questions to help your visitors.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Settings Tab -->
            <div id="settings-tab" class="tab-content">
                <form id="settings-form" method="POST">
                    <input type="hidden" name="update_settings" value="1">
                    
                    <div class="settings-container">
                        <!-- Contact Info Settings -->
                        <div class="settings-category">
                            <h3><i class="ri-contacts-line"></i> Contact Information</h3>
                            <div class="settings-grid">
                                <?php if (isset($settings['contact_info'])): ?>
                                    <?php foreach ($settings['contact_info'] as $setting): ?>
                                        <div class="setting-item">
                                            <label for="setting_<?php echo $setting['setting_key']; ?>">
                                                <?php echo htmlspecialchars($setting['display_name']); ?>
                                            </label>
                                            <?php if ($setting['setting_type'] == 'textarea'): ?>
                                                <textarea id="setting_<?php echo $setting['setting_key']; ?>" 
                                                          name="settings[<?php echo $setting['setting_key']; ?>]"
                                                          rows="3"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                            <?php else: ?>
                                                <input type="<?php echo $setting['setting_type'] == 'email' ? 'email' : ($setting['setting_type'] == 'phone' ? 'tel' : 'text'); ?>" 
                                                       id="setting_<?php echo $setting['setting_key']; ?>" 
                                                       name="settings[<?php echo $setting['setting_key']; ?>]"
                                                       value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                       <?php echo $setting['setting_type'] == 'email' ? 'pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"' : ''; ?>
                                                       <?php echo $setting['setting_type'] == 'phone' ? 'pattern="[0-9+\s\-\(\)]{10,}"' : ''; ?>>
                                            <?php endif; ?>
                                            <?php if ($setting['setting_type'] == 'phone'): ?>
                                                <div class="setting-hint">Format: +91 1234567890</div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Social Media Settings -->
                        <div class="settings-category">
                            <h3><i class="ri-share-line"></i> Social Media Links</h3>
                            <div class="settings-grid">
                                <?php if (isset($settings['social_media'])): ?>
                                    <?php foreach ($settings['social_media'] as $setting): ?>
                                        <div class="setting-item">
                                            <label for="setting_<?php echo $setting['setting_key']; ?>">
                                                <i class="ri-<?php echo str_replace('social_', '', $setting['setting_key']); ?>-fill"></i>
                                                <?php echo htmlspecialchars($setting['display_name']); ?>
                                            </label>
                                            <input type="url" 
                                                   id="setting_<?php echo $setting['setting_key']; ?>" 
                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                   value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                                   placeholder="https://example.com/username">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- FAQ Categories -->
                        <div class="settings-category">
                            <h3><i class="ri-list-settings-line"></i> FAQ Categories</h3>
                            <div class="settings-grid">
                                <?php if (isset($settings['faq_categories'])): ?>
                                    <?php foreach ($settings['faq_categories'] as $setting): ?>
                                        <div class="setting-item">
                                            <label for="setting_<?php echo $setting['setting_key']; ?>">
                                                <?php echo htmlspecialchars($setting['display_name']); ?>
                                            </label>
                                            <input type="text" 
                                                   id="setting_<?php echo $setting['setting_key']; ?>" 
                                                   name="settings[<?php echo $setting['setting_key']; ?>]"
                                                   value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div style="margin-top: 32px; text-align: right;">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Save All Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Message Details Modal -->
    <div class="modal" id="message-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeMessageModal()">&times;</span>
            <div class="modal-header">
                <h2>Message Details</h2>
            </div>
            <div class="modal-body">
                <div id="message-details-content"></div>
            </div>
        </div>
    </div>

    <!-- Reply Modal -->
    <div class="modal" id="reply-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeReplyModal()">&times;</span>
            <h2>Reply to Message</h2>
            <form id="reply-form" method="POST">
                <input type="hidden" id="reply_message_id" name="message_id">
                <input type="hidden" name="send_reply" value="1">
                
                <div class="form-group">
                    <label for="reply_subject">Subject *</label>
                    <input type="text" id="reply_subject" name="reply_subject" required>
                </div>
                
                <div class="form-group">
                    <label for="reply_message">Message *</label>
                    <textarea id="reply_message" name="reply_message" rows="8" required placeholder="Type your reply here..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="ri-send-plane-line"></i> Send Reply
                </button>
            </form>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal" id="status-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeStatusModal()">&times;</span>
            <h2>Update Message Status</h2>
            <form id="status-form" method="POST">
                <input type="hidden" id="status_message_id" name="message_id">
                <input type="hidden" name="update_message_status" value="1">
                
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="new">New</option>
                        <option value="read">Read</option>
                        <option value="replied">Replied</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="admin_notes">Admin Notes (Optional)</label>
                    <textarea id="admin_notes" name="admin_notes" rows="4" placeholder="Add any notes about this message..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="ri-check-line"></i> Update Status
                </button>
            </form>
        </div>
    </div>

    <!-- Add/Edit FAQ Modal -->
    <div class="modal" id="faq-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeFAQModal()">&times;</span>
            <h2 id="faq-modal-title">Add FAQ</h2>
            <form id="faq-form" method="POST">
                <input type="hidden" id="faq_id" name="faq_id">
                <input type="hidden" name="add_faq" id="faq-form-action">
                
                <div class="form-group">
                    <label for="faq_question">Question *</label>
                    <input type="text" id="faq_question" name="question" required>
                </div>
                
                <div class="form-group">
                    <label for="faq_answer">Answer *</label>
                    <textarea id="faq_answer" name="answer" rows="6" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="faq_category">Category *</label>
                        <select id="faq_category" name="category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($faq_categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>">
                                    <?php echo htmlspecialchars($category); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="faq_sort_order">Sort Order</label>
                        <input type="number" id="faq_sort_order" name="sort_order" min="0" value="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="faq_is_active" name="is_active" value="1" checked>
                        <label for="faq_is_active">Active (visible on website)</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="ri-save-line"></i> Save FAQ
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
            const tabIndex = tabName === 'messages' ? 1 : tabName === 'faqs' ? 2 : 3;
            document.querySelector(`.tab:nth-child(${tabIndex})`).classList.add('active');
            document.getElementById(`${tabName}-tab`).classList.add('active');
        }

        // Modal functions
        function viewMessage(messageId) {
            fetch(`../logic/get_message.php?id=${messageId}`)
                .then(response => response.json())
                .then(data => {
                    const subjectLabels = {
                        'general': 'General Inquiry',
                        'booking': 'Booking Information',
                        'custom': 'Custom Package Request',
                        'feedback': 'Feedback',
                        'complaint': 'Complaint',
                        'other': 'Other'
                    };
                    
                    const content = `
                        <div class="message-details">
                            <div class="message-details-item">
                                <strong>From:</strong>
                                <span>${data.name} &lt;${data.email}&gt;</span>
                            </div>
                            <div class="message-details-item">
                                <strong>Phone:</strong>
                                <span>${data.phone || 'Not provided'}</span>
                            </div>
                            <div class="message-details-item">
                                <strong>Subject:</strong>
                                <span>${subjectLabels[data.subject] || data.subject}</span>
                            </div>
                            <div class="message-details-item">
                                <strong>Date:</strong>
                                <span>${new Date(data.created_at).toLocaleString()}</span>
                            </div>
                            <div class="message-details-item">
                                <strong>Status:</strong>
                                <span class="status-badge status-${data.status}">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span>
                            </div>
                        </div>
                        
                        <div class="message-content">
                            <h4 style="margin-bottom: 16px; color: var(--text-primary);">Message:</h4>
                            <p>${data.message.replace(/\n/g, '<br>')}</p>
                        </div>
                        
                        ${data.admin_notes ? `
                        <div class="admin-notes">
                            <h4>Admin Notes:</h4>
                            <p>${data.admin_notes.replace(/\n/g, '<br>')}</p>
                        </div>
                        ` : ''}
                        
                        <div style="display: flex; gap: 12px; margin-top: 24px;">
                            <button class="btn btn-primary" onclick="replyMessage(${data.id})">
                                <i class="ri-reply-line"></i> Reply
                            </button>
                            <button class="btn btn-warning" onclick="updateMessageStatus(${data.id})">
                                <i class="ri-edit-line"></i> Update Status
                            </button>
                        </div>
                    `;
                    
                    document.getElementById('message-details-content').innerHTML = content;
                    document.getElementById('message-modal').classList.add('active');
                    
                    // Mark as read if it's new
                    if (data.status === 'new') {
                        updateMessageStatusSilent(data.id, 'read');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading message details');
                });
        }

        function replyMessage(messageId) {
            fetch(`../logic/get_message.php?id=${messageId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('reply_message_id').value = messageId;
                    document.getElementById('reply_subject').value = `Re: ${data.subject}`;
                    document.getElementById('reply-modal').classList.add('active');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading message details');
                });
        }

        function updateMessageStatus(messageId) {
            document.getElementById('status_message_id').value = messageId;
            document.getElementById('status-modal').classList.add('active');
        }

        function updateMessageStatusSilent(messageId, status) {
            const formData = new FormData();
            formData.append('message_id', messageId);
            formData.append('status', status);
            formData.append('update_message_status', '1');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            });
        }

        function openAddFAQModal() {
            document.getElementById('faq-modal-title').textContent = 'Add FAQ';
            document.getElementById('faq-form-action').value = 'add_faq';
            document.getElementById('faq-form').reset();
            document.getElementById('faq_id').value = '';
            document.getElementById('faq-modal').classList.add('active');
        }

        function editFAQ(faqId) {
            fetch(`../logic/get_faq.php?id=${faqId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('faq-modal-title').textContent = 'Edit FAQ';
                    document.getElementById('faq-form-action').value = 'update_faq';
                    document.getElementById('faq_id').value = data.id;
                    document.getElementById('faq_question').value = data.question;
                    document.getElementById('faq_answer').value = data.answer;
                    document.getElementById('faq_category').value = data.category;
                    document.getElementById('faq_sort_order').value = data.sort_order;
                    document.getElementById('faq_is_active').checked = data.is_active == 1;
                    
                    document.getElementById('faq-modal').classList.add('active');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading FAQ details');
                });
        }

        function deleteFAQ(faqId) {
            if (confirm('Are you sure you want to delete this FAQ?')) {
                window.location.href = `?delete_faq=${faqId}`;
            }
        }

        function closeMessageModal() {
            document.getElementById('message-modal').classList.remove('active');
        }

        function closeReplyModal() {
            document.getElementById('reply-modal').classList.remove('active');
        }

        function closeStatusModal() {
            document.getElementById('status-modal').classList.remove('active');
        }

        function closeFAQModal() {
            document.getElementById('faq-modal').classList.remove('active');
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
        document.getElementById('settings-form').addEventListener('submit', function(e) {
            // Validate email fields
            const emailInputs = this.querySelectorAll('input[type="email"]');
            emailInputs.forEach(input => {
                if (input.value && !input.validity.valid) {
                    e.preventDefault();
                    alert(`Please enter a valid email address for ${input.previousElementSibling.textContent}`);
                    input.focus();
                    return false;
                }
            });
            
            return true;
        });

        document.getElementById('reply-form').addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                alert('Please fill all required fields');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Sending...';
            submitBtn.disabled = true;
            
            // Re-enable button after 5 seconds if form doesn't submit
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
            
            return true;
        });

        // Add CSS for spinner
        const style = document.createElement('style');
        style.textContent = `
            .animate-spin {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Update chart bar heights on window resize
            function updateChartHeights() {
                const chart = document.getElementById('message-chart');
                if (chart) {
                    const bars = chart.querySelectorAll('.chart-bar');
                    const maxHeight = chart.clientHeight - 50; // Subtract space for labels
                    
                    bars.forEach(bar => {
                        const currentHeight = parseFloat(bar.style.height);
                        bar.style.height = (currentHeight / 100 * maxHeight) + 'px';
                    });
                }
            }
            
            window.addEventListener('resize', updateChartHeights);
            updateChartHeights();
            
            // Auto-refresh messages every 30 seconds
            setInterval(() => {
                if (document.getElementById('messages-tab').classList.contains('active')) {
                    window.location.reload();
                }
            }, 30000);
        });
    </script>
</body>
</html>