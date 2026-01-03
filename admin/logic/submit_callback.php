<?php
// logic/submit_callback.php
require_once '../includes/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $honeypot = $_POST['website'] ?? ''; // Hidden field for bots
    $human_check = $_POST['human_check'] ?? '';
    
    // Bot detection
    $is_bot = false;
    
    // Check honeypot
    if (!empty($honeypot)) {
        $is_bot = true;
    }
    
    // Check human verification (simple math)
    if ($human_check !== '7') { // 3 + 4 = 7
        $is_bot = true;
    }
    
    // Check for rapid submissions from same IP (optional)
    $ip = $_SERVER['REMOTE_ADDR'];
    $recent_submissions = $conn->query("SELECT COUNT(*) as count FROM callback_leads WHERE ip_address = '$ip' AND submitted_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)")->fetch_assoc()['count'];
    
    if ($recent_submissions > 2) {
        $is_bot = true;
    }
    
    if (empty($name) || empty($phone)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all fields']);
        exit;
    }
    
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo json_encode(['success' => false, 'message' => 'Invalid phone number']);
        exit;
    }
    
    // Insert into database
    $source_page = $_SERVER['HTTP_REFERER'] ?? 'Direct';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO callback_leads (name, phone, source_page, ip_address, user_agent, is_bot) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $name, $phone, $source_page, $ip, $user_agent, $is_bot);
    
    if ($stmt->execute()) {
        // Send WhatsApp notification (optional)
        $whatsapp_message = "New callback request from *$name*\nPhone: $phone\nTime: " . date('Y-m-d H:i:s');
        // You can integrate WhatsApp API here
        
        echo json_encode(['success' => true, 'message' => 'We will call you shortly!', 'is_bot' => $is_bot]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>