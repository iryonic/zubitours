<?php
require_once '../admin/connection.php';

if (isset($_POST['image_id'])) {
    $image_id = intval($_POST['image_id']);
    
    // Update view count in database
    $sql = "UPDATE gallery SET views = COALESCE(views, 0) + 1 WHERE id = $image_id";
    mysqli_query($conn, $sql);
    
    // Log view in separate table (optional)
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    
    $log_sql = "INSERT INTO gallery_views (image_id, ip_address, user_agent, referrer) 
                VALUES ($image_id, '$ip', '$user_agent', '$referrer')";
    mysqli_query($conn, $log_sql);
}

echo json_encode(['success' => true]);
?>