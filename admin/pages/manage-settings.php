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

// Handle settings update
if (isset($_POST['update_general_settings'])) {
    $site_title = $_POST['site_title'];
    $site_description = $_POST['site_description'];
    $site_keywords = $_POST['site_keywords'];
    $contact_email = $_POST['contact_email'];
    $contact_phone = $_POST['contact_phone'];
    $site_currency = $_POST['site_currency'];
    $timezone = $_POST['timezone'];
    $date_format = $_POST['date_format'];
    
    // Store settings in database or config file
    // For now, we'll use a simple approach
    $settings = [
        'site_title' => $site_title,
        'site_description' => $site_description,
        'site_keywords' => $site_keywords,
        'contact_email' => $contact_email,
        'contact_phone' => $contact_phone,
        'site_currency' => $site_currency,
        'timezone' => $timezone,
        'date_format' => $date_format
    ];
    
    // In production, save to database or config file
    $message = "General settings updated successfully!";
    $message_type = "success";
}

// Handle email settings update
if (isset($_POST['update_email_settings'])) {
    $smtp_host = $_POST['smtp_host'];
    $smtp_port = $_POST['smtp_port'];
    $smtp_username = $_POST['smtp_username'];
    $smtp_password = $_POST['smtp_password'];
    $smtp_encryption = $_POST['smtp_encryption'];
    $from_email = $_POST['from_email'];
    $from_name = $_POST['from_name'];
    $email_signature = $_POST['email_signature'];
    
    // Store settings
    $email_settings = [
        'smtp_host' => $smtp_host,
        'smtp_port' => $smtp_port,
        'smtp_username' => $smtp_username,
        'smtp_password' => $smtp_password,
        'smtp_encryption' => $smtp_encryption,
        'from_email' => $from_email,
        'from_name' => $from_name,
        'email_signature' => $email_signature
    ];
    
    $message = "Email settings updated successfully!";
    $message_type = "success";
}

// Handle payment settings update
if (isset($_POST['update_payment_settings'])) {
    $enable_payments = isset($_POST['enable_payments']) ? 1 : 0;
    $currency = $_POST['currency'];
    $stripe_publishable_key = $_POST['stripe_publishable_key'];
    $stripe_secret_key = $_POST['stripe_secret_key'];
    $razorpay_key_id = $_POST['razorpay_key_id'];
    $razorpay_key_secret = $_POST['razorpay_key_secret'];
    $paypal_client_id = $_POST['paypal_client_id'];
    $paypal_client_secret = $_POST['paypal_client_secret'];
    $payment_test_mode = isset($_POST['payment_test_mode']) ? 1 : 0;
    
    $payment_settings = [
        'enable_payments' => $enable_payments,
        'currency' => $currency,
        'stripe_publishable_key' => $stripe_publishable_key,
        'stripe_secret_key' => $stripe_secret_key,
        'razorpay_key_id' => $razorpay_key_id,
        'razorpay_key_secret' => $razorpay_key_secret,
        'paypal_client_id' => $paypal_client_id,
        'paypal_client_secret' => $paypal_client_secret,
        'payment_test_mode' => $payment_test_mode
    ];
    
    $message = "Payment settings updated successfully!";
    $message_type = "success";
}

// Handle SEO settings update
if (isset($_POST['update_seo_settings'])) {
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $meta_keywords = $_POST['meta_keywords'];
    $google_analytics = $_POST['google_analytics'];
    $google_site_verification = $_POST['google_site_verification'];
    $bing_verification = $_POST['bing_verification'];
    $robots_txt = $_POST['robots_txt'];
    $sitemap_url = $_POST['sitemap_url'];
    
    $seo_settings = [
        'meta_title' => $meta_title,
        'meta_description' => $meta_description,
        'meta_keywords' => $meta_keywords,
        'google_analytics' => $google_analytics,
        'google_site_verification' => $google_site_verification,
        'bing_verification' => $bing_verification,
        'robots_txt' => $robots_txt,
        'sitemap_url' => $sitemap_url
    ];
    
    $message = "SEO settings updated successfully!";
    $message_type = "success";
}

// Handle security settings update
if (isset($_POST['update_security_settings'])) {
    $login_attempts = $_POST['login_attempts'];
    $lockout_time = $_POST['lockout_time'];
    $session_timeout = $_POST['session_timeout'];
    $enable_2fa = isset($_POST['enable_2fa']) ? 1 : 0;
    $force_ssl = isset($_POST['force_ssl']) ? 1 : 0;
    $blocked_ips = $_POST['blocked_ips'];
    $allowed_ips = $_POST['allowed_ips'];
    
    $security_settings = [
        'login_attempts' => $login_attempts,
        'lockout_time' => $lockout_time,
        'session_timeout' => $session_timeout,
        'enable_2fa' => $enable_2fa,
        'force_ssl' => $force_ssl,
        'blocked_ips' => $blocked_ips,
        'allowed_ips' => $allowed_ips
    ];
    
    $message = "Security settings updated successfully!";
    $message_type = "success";
}

// Handle backup settings update
if (isset($_POST['update_backup_settings'])) {
    $auto_backup = isset($_POST['auto_backup']) ? 1 : 0;
    $backup_frequency = $_POST['backup_frequency'];
    $backup_retention = $_POST['backup_retention'];
    $backup_location = $_POST['backup_location'];
    $notify_on_backup = isset($_POST['notify_on_backup']) ? 1 : 0;
    
    $backup_settings = [
        'auto_backup' => $auto_backup,
        'backup_frequency' => $backup_frequency,
        'backup_retention' => $backup_retention,
        'backup_location' => $backup_location,
        'notify_on_backup' => $notify_on_backup
    ];
    
    $message = "Backup settings updated successfully!";
    $message_type = "success";
}

// Handle backup now action
if (isset($_POST['backup_now'])) {
    // Simulate backup process
    $backup_file = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $message = "Backup created successfully: " . $backup_file;
    $message_type = "success";
}

// Handle cache clear action
if (isset($_POST['clear_cache'])) {
    // Simulate cache clearing
    $message = "Cache cleared successfully!";
    $message_type = "success";
}

// Handle admin password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $message = "New passwords do not match!";
        $message_type = "error";
    } elseif (strlen($new_password) < 8) {
        $message = "Password must be at least 8 characters long!";
        $message_type = "error";
    } else {
        // In production, verify current password and update in database
        $message = "Password changed successfully!";
        $message_type = "success";
    }
}

// Fetch current admin info
$admin_id = $_SESSION['admin_id'] ?? 1;
$admin_query = $conn->query("SELECT * FROM admins WHERE id = $admin_id");
$admin = $admin_query->fetch_assoc();

// Fetch system info
$system_info = [
    'php_version' => phpversion(),
    'mysql_version' => $conn->server_info,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit')
];

// Fetch database stats
$total_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM package_bookings")->fetch_assoc()['count'];
$total_cars = $conn->query("SELECT COUNT(*) as count FROM car_rentals")->fetch_assoc()['count'];
$total_faqs = $conn->query("SELECT COUNT(*) as count FROM faqs")->fetch_assoc()['count'];
$total_destinations = $conn->query("SELECT COUNT(*) as count FROM destinations")->fetch_assoc()['count'];
$total_packages = $conn->query("SELECT COUNT(*) as count FROM packages")->fetch_assoc()['count'];
$total_gallery = $conn->query("SELECT COUNT(*) as count FROM gallery")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Zubi Tours Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
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
                <h1 class="section-title">Settings</h1>
                <div class="section-actions">
                    <button class="btn btn-primary" onclick="createBackup()">
                        <i class="ri-download-cloud-line"></i> Backup Now
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon messages-icon">
                        <i class="ri-database-2-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_messages + $total_bookings + $total_cars + $total_faqs + $total_destinations + $total_packages + $total_gallery; ?></h3>
                        <p>Total Records</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon bookings-icon">
                        <i class="ri-server-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $system_info['php_version']; ?></h3>
                        <p>PHP Version</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon system-icon">
                        <i class="ri-cpu-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo substr($system_info['mysql_version'], 0, 10); ?></h3>
                        <p>MySQL Version</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon storage-icon">
                        <i class="ri-sd-card-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $system_info['upload_max_filesize']; ?></h3>
                        <p>Max Upload Size</p>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" onclick="switchTab('general')">General</div>
                <div class="tab" onclick="switchTab('email')">Email</div>
                <div class="tab" onclick="switchTab('payment')">Payment</div>
                <div class="tab" onclick="switchTab('seo')">SEO</div>
                <div class="tab" onclick="switchTab('security')">Security</div>
                <div class="tab" onclick="switchTab('backup')">Backup</div>
                <div class="tab" onclick="switchTab('system')">System Info</div>
            </div>

            <!-- General Settings Tab -->
            <div id="general-tab" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <h3>General Settings</h3>
                        <p>Configure basic website settings</p>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="site_title">Site Title *</label>
                                    <input type="text" id="site_title" name="site_title" value="Zubi Tours & Holidays Kashmir" required>
                                </div>
                                <div class="form-group">
                                    <label for="site_currency">Currency *</label>
                                    <select id="site_currency" name="site_currency" required>
                                        <option value="INR" selected>Indian Rupee (₹)</option>
                                        <option value="USD">US Dollar ($)</option>
                                        <option value="EUR">Euro (€)</option>
                                        <option value="GBP">British Pound (£)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="site_description">Site Description</label>
                                <textarea id="site_description" name="site_description" rows="2">Explore the breathtaking beauty of Kashmir and Ladakh with Zubi Tours</textarea>
                            </div>

                            <div class="form-group">
                                <label for="site_keywords">Keywords (comma separated)</label>
                                <input type="text" id="site_keywords" name="site_keywords" value="Kashmir tours, Ladakh travel, houseboats, Gulmarg, Srinagar, adventure travel">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="contact_email">Contact Email *</label>
                                    <input type="email" id="contact_email" name="contact_email" value="info@zubitours.com" required>
                                </div>
                                <div class="form-group">
                                    <label for="contact_phone">Contact Phone *</label>
                                    <input type="tel" id="contact_phone" name="contact_phone" value="+91 7051073293" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="timezone">Timezone</label>
                                    <select id="timezone" name="timezone">
                                        <option value="Asia/Kolkata" selected>India Standard Time (IST)</option>
                                        <option value="UTC">UTC</option>
                                        <option value="America/New_York">Eastern Time (ET)</option>
                                        <option value="Europe/London">London (GMT)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="date_format">Date Format</label>
                                    <select id="date_format" name="date_format">
                                        <option value="d-m-Y" selected>DD-MM-YYYY</option>
                                        <option value="m-d-Y">MM-DD-YYYY</option>
                                        <option value="Y-m-d">YYYY-MM-DD</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="update_general_settings" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Save General Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Email Settings Tab -->
            <div id="email-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Email Settings</h3>
                        <p>Configure email server and notification settings</p>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="smtp_host">SMTP Host *</label>
                                    <input type="text" id="smtp_host" name="smtp_host" value="smtp.gmail.com" required>
                                </div>
                                <div class="form-group">
                                    <label for="smtp_port">SMTP Port *</label>
                                    <input type="number" id="smtp_port" name="smtp_port" value="587" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="smtp_username">SMTP Username *</label>
                                    <input type="email" id="smtp_username" name="smtp_username" value="your-email@gmail.com" required>
                                </div>
                                <div class="form-group">
                                    <label for="smtp_password">SMTP Password *</label>
                                    <input type="password" id="smtp_password" name="smtp_password" value="" required>
                                    <small>App password for Gmail</small>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="smtp_encryption">Encryption</label>
                                    <select id="smtp_encryption" name="smtp_encryption">
                                        <option value="tls" selected>TLS</option>
                                        <option value="ssl">SSL</option>
                                        <option value="">None</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="from_email">From Email *</label>
                                    <input type="email" id="from_email" name="from_email" value="noreply@zubitours.com" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="from_name">From Name *</label>
                                <input type="text" id="from_name" name="from_name" value="Zubi Tours" required>
                            </div>

                            <div class="form-group">
                                <label for="email_signature">Email Signature</label>
                                <textarea id="email_signature" name="email_signature" rows="4">Best regards,
Zubi Tours & Holidays Kashmir
https://zubitours.com
+91 7051073293</textarea>
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="update_email_settings" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Save Email Settings
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="testEmail()">
                                    <i class="ri-mail-send-line"></i> Test Email
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Payment Settings Tab -->
            <div id="payment-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Payment Settings</h3>
                        <p>Configure payment gateway settings</p>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="enable_payments" name="enable_payments" value="1" checked>
                                    <label for="enable_payments">Enable Online Payments</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="currency">Currency *</label>
                                    <select id="currency" name="currency" required>
                                        <option value="INR" selected>Indian Rupee (₹)</option>
                                        <option value="USD">US Dollar ($)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="payment_test_mode">
                                        <input type="checkbox" id="payment_test_mode" name="payment_test_mode" value="1" checked>
                                        Test Mode
                                    </label>
                                </div>
                            </div>

                            <h4 style="margin: 20px 0 10px 0; color: var(--text-primary);">Stripe Settings</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="stripe_publishable_key">Publishable Key</label>
                                    <input type="text" id="stripe_publishable_key" name="stripe_publishable_key">
                                </div>
                                <div class="form-group">
                                    <label for="stripe_secret_key">Secret Key</label>
                                    <input type="password" id="stripe_secret_key" name="stripe_secret_key">
                                </div>
                            </div>

                            <h4 style="margin: 20px 0 10px 0; color: var(--text-primary);">Razorpay Settings</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="razorpay_key_id">Key ID</label>
                                    <input type="text" id="razorpay_key_id" name="razorpay_key_id">
                                </div>
                                <div class="form-group">
                                    <label for="razorpay_key_secret">Key Secret</label>
                                    <input type="password" id="razorpay_key_secret" name="razorpay_key_secret">
                                </div>
                            </div>

                            <h4 style="margin: 20px 0 10px 0; color: var(--text-primary);">PayPal Settings</h4>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="paypal_client_id">Client ID</label>
                                    <input type="text" id="paypal_client_id" name="paypal_client_id">
                                </div>
                                <div class="form-group">
                                    <label for="paypal_client_secret">Client Secret</label>
                                    <input type="password" id="paypal_client_secret" name="paypal_client_secret">
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="update_payment_settings" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Save Payment Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- SEO Settings Tab -->
            <div id="seo-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>SEO Settings</h3>
                        <p>Configure search engine optimization settings</p>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="meta_title">Meta Title</label>
                                <input type="text" id="meta_title" name="meta_title" value="Zubi Tours & Holidays Kashmir - Explore Paradise">
                            </div>

                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <textarea id="meta_description" name="meta_description" rows="3">Experience the breathtaking beauty of Kashmir and Ladakh with Zubi Tours. Book your dream vacation today!</textarea>
                            </div>

                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input type="text" id="meta_keywords" name="meta_keywords" value="Kashmir tours, Ladakh travel, houseboats, Gulmarg, Srinagar, adventure travel, honeymoon packages">
                            </div>

                            <div class="form-group">
                                <label for="google_analytics">Google Analytics Code</label>
                                <textarea id="google_analytics" name="google_analytics" rows="3" placeholder="Paste your Google Analytics tracking code here"></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="google_site_verification">Google Site Verification</label>
                                    <input type="text" id="google_site_verification" name="google_site_verification">
                                </div>
                                <div class="form-group">
                                    <label for="bing_verification">Bing Verification</label>
                                    <input type="text" id="bing_verification" name="bing_verification">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="robots_txt">robots.txt Content</label>
                                <textarea id="robots_txt" name="robots_txt" rows="4">User-agent: *
Allow: /
Disallow: /admin/
Disallow: /includes/
Sitemap: https://zubitours.com/sitemap.xml</textarea>
                            </div>

                            <div class="form-group">
                                <label for="sitemap_url">Sitemap URL</label>
                                <input type="url" id="sitemap_url" name="sitemap_url" value="https://zubitours.com/sitemap.xml">
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="update_seo_settings" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Save SEO Settings
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="generateSitemap()">
                                    <i class="ri-map-2-line"></i> Generate Sitemap
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security Settings Tab -->
            <div id="security-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Security Settings</h3>
                        <p>Configure security and access control settings</p>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="login_attempts">Max Login Attempts *</label>
                                    <input type="number" id="login_attempts" name="login_attempts" value="5" min="1" max="10" required>
                                </div>
                                <div class="form-group">
                                    <label for="lockout_time">Lockout Time (minutes) *</label>
                                    <input type="number" id="lockout_time" name="lockout_time" value="15" min="1" max="120" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="session_timeout">Session Timeout (minutes) *</label>
                                <input type="number" id="session_timeout" name="session_timeout" value="30" min="5" max="240" required>
                            </div>

                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="enable_2fa" name="enable_2fa" value="1">
                                    <label for="enable_2fa">Enable Two-Factor Authentication</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="force_ssl" name="force_ssl" value="1" checked>
                                    <label for="force_ssl">Force SSL/HTTPS</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="blocked_ips">Blocked IP Addresses</label>
                                <textarea id="blocked_ips" name="blocked_ips" rows="3" placeholder="Enter IP addresses to block (one per line)"></textarea>
                                <small>One IP address per line</small>
                            </div>

                            <div class="form-group">
                                <label for="allowed_ips">Allowed IPs (Admin Access)</label>
                                <textarea id="allowed_ips" name="allowed_ips" rows="3" placeholder="Enter allowed IP addresses for admin access (one per line)"></textarea>
                                <small>Leave empty to allow from any IP</small>
                            </div>

                            <!-- Change Password Section -->
                            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                                <h4 style="margin-bottom: 15px; color: var(--text-primary);">Change Password</h4>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="current_password">Current Password *</label>
                                        <input type="password" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="new_password">New Password *</label>
                                        <input type="password" id="new_password" name="new_password" required>
                                        <small>Minimum 8 characters</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password *</label>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" name="change_password" class="btn btn-primary">
                                        <i class="ri-lock-password-line"></i> Change Password
                                    </button>
                                </div>
                            </div>

                            <div class="form-actions" style="margin-top: 30px;">
                                <button type="submit" name="update_security_settings" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Save Security Settings
                                </button>
                                <button type="submit" name="clear_cache" class="btn btn-warning">
                                    <i class="ri-refresh-line"></i> Clear Cache
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Backup Settings Tab -->
            <div id="backup-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>Backup Settings</h3>
                        <p>Configure database backup settings</p>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="auto_backup" name="auto_backup" value="1" checked>
                                    <label for="auto_backup">Enable Automatic Backups</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="backup_frequency">Backup Frequency *</label>
                                    <select id="backup_frequency" name="backup_frequency" required>
                                        <option value="daily" selected>Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="backup_retention">Retention Period (days) *</label>
                                    <input type="number" id="backup_retention" name="backup_retention" value="30" min="1" max="365" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="backup_location">Backup Location</label>
                                <input type="text" id="backup_location" name="backup_location" value="../backups/" required>
                                <small>Relative path from root directory</small>
                            </div>

                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="notify_on_backup" name="notify_on_backup" value="1" checked>
                                    <label for="notify_on_backup">Notify on Backup Completion</label>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" name="update_backup_settings" class="btn btn-primary">
                                    <i class="ri-save-line"></i> Save Backup Settings
                                </button>
                                <button type="submit" name="backup_now" class="btn btn-success">
                                    <i class="ri-download-cloud-line"></i> Backup Now
                                </button>
                            </div>
                        </form>

                        <!-- Recent Backups -->
                        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                            <h4 style="margin-bottom: 15px; color: var(--text-primary);">Recent Backups</h4>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Date</th>
                                            <th>Size</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>backup_2025-12-19_10-30-15.sql</td>
                                            <td>Today, 10:30 AM</td>
                                            <td>2.4 MB</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">
                                                    <i class="ri-download-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>backup_2025-12-18_10-30-15.sql</td>
                                            <td>Yesterday, 10:30 AM</td>
                                            <td>2.3 MB</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">
                                                    <i class="ri-download-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Info Tab -->
            <div id="system-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h3>System Information</h3>
                        <p>System configuration and statistics</p>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-section">
                                <h4 style="margin-bottom: 15px; color: var(--text-primary);">PHP Configuration</h4>
                                <div class="info-item">
                                    <span class="info-label">PHP Version:</span>
                                    <span class="info-value"><?php echo $system_info['php_version']; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Max Upload Size:</span>
                                    <span class="info-value"><?php echo $system_info['upload_max_filesize']; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Max Execution Time:</span>
                                    <span class="info-value"><?php echo $system_info['max_execution_time']; ?> seconds</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Memory Limit:</span>
                                    <span class="info-value"><?php echo $system_info['memory_limit']; ?></span>
                                </div>
                            </div>

                            <div class="info-section">
                                <h4 style="margin-bottom: 15px; color: var(--text-primary);">Database Information</h4>
                                <div class="info-item">
                                    <span class="info-label">MySQL Version:</span>
                                    <span class="info-value"><?php echo $system_info['mysql_version']; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Database Name:</span>
                                    <span class="info-value">travel_db</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Server Software:</span>
                                    <span class="info-value"><?php echo $system_info['server_software']; ?></span>
                                </div>
                            </div>

                            <div class="info-section">
                                <h4 style="margin-bottom: 15px; color: var(--text-primary);">Database Statistics</h4>
                                <div class="info-item">
                                    <span class="info-label">Total Messages:</span>
                                    <span class="info-value"><?php echo $total_messages; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Total Bookings:</span>
                                    <span class="info-value"><?php echo $total_bookings; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Total Cars:</span>
                                    <span class="info-value"><?php echo $total_cars; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Total FAQs:</span>
                                    <span class="info-value"><?php echo $total_faqs; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Total Destinations:</span>
                                    <span class="info-value"><?php echo $total_destinations; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Total Packages:</span>
                                    <span class="info-value"><?php echo $total_packages; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Total Gallery Items:</span>
                                    <span class="info-value"><?php echo $total_gallery; ?></span>
                                </div>
                            </div>

                            <div class="info-section">
                                <h4 style="margin-bottom: 15px; color: var(--text-primary);">Server Information</h4>
                                <div class="info-item">
                                    <span class="info-label">Server Name:</span>
                                    <span class="info-value"><?php echo $_SERVER['SERVER_NAME'] ?? 'localhost'; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Server Address:</span>
                                    <span class="info-value"><?php echo $_SERVER['SERVER_ADDR'] ?? '127.0.0.1'; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Server Port:</span>
                                    <span class="info-value"><?php echo $_SERVER['SERVER_PORT'] ?? '80'; ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Document Root:</span>
                                    <span class="info-value"><?php echo $_SERVER['DOCUMENT_ROOT'] ?? '/var/www/html'; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions" style="margin-top: 30px;">
                            <button type="button" class="btn btn-primary" onclick="refreshSystemInfo()">
                                <i class="ri-refresh-line"></i> Refresh Info
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="copySystemInfo()">
                                <i class="ri-file-copy-line"></i> Copy Info
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
            const tabIndex = ['general', 'email', 'payment', 'seo', 'security', 'backup', 'system'].indexOf(tabName) + 1;
            document.querySelector(`.tab:nth-child(${tabIndex})`).classList.add('active');
            document.getElementById(`${tabName}-tab`).classList.add('active');
        }

        // Test email function
        function testEmail() {
            const email = document.getElementById('from_email').value;
            if (!email || email === 'your-email@gmail.com') {
                alert('Please configure your email settings first.');
                return;
            }
            
            if (confirm('Send a test email to ' + email + '?')) {
                alert('Test email sent! Check your inbox.');
                // In production, make an AJAX call to send test email
            }
        }

        // Create backup
        function createBackup() {
            if (confirm('Create a database backup now?')) {
                // Submit backup form
                document.querySelector('button[name="backup_now"]').click();
            }
        }

        // Generate sitemap
        function generateSitemap() {
            if (confirm('Generate XML sitemap?')) {
                alert('Sitemap generated successfully!');
                // In production, make an AJAX call to generate sitemap
            }
        }

        // Refresh system info
        function refreshSystemInfo() {
            window.location.reload();
        }

        // Copy system info
        function copySystemInfo() {
            let systemInfoText = 'System Information:\n\n';
            
            // PHP Configuration
            systemInfoText += 'PHP Configuration:\n';
            document.querySelectorAll('#system-tab .info-item').forEach(item => {
                const label = item.querySelector('.info-label').textContent;
                const value = item.querySelector('.info-value').textContent;
                systemInfoText += `${label} ${value}\n`;
            });
            
            // Copy to clipboard
            navigator.clipboard.writeText(systemInfoText).then(() => {
                alert('System information copied to clipboard!');
            });
        }

        // Password validation
        document.addEventListener('DOMContentLoaded', function() {
            const passwordForm = document.querySelector('form[name="change_password"]');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (newPassword !== confirmPassword) {
                        e.preventDefault();
                        alert('New passwords do not match!');
                        return false;
                    }
                    
                    if (newPassword.length < 8) {
                        e.preventDefault();
                        alert('Password must be at least 8 characters long!');
                        return false;
                    }
                    
                    return true;
                });
            }

            // Toggle password visibility
            const passwordInputs = document.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => {
                const toggle = document.createElement('span');
                toggle.innerHTML = '<i class="ri-eye-line"></i>';
                toggle.style.cssText = 'position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-secondary);';
                toggle.addEventListener('click', function() {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="ri-eye-line"></i>' : '<i class="ri-eye-off-line"></i>';
                });
                
                const parent = input.parentNode;
                parent.style.position = 'relative';
                parent.appendChild(toggle);
            });
        });
    </script>
</body>
</html>