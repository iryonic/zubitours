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
    <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
    <!-- Sidebar -->
     <?php
        include '../includes/sidebar.php';
   ?>
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