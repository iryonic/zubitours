<?php
session_start();
require_once '../includes/connection.php';

// Redirect if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Fetch destination images
    $stmt = $conn->prepare("SELECT * FROM destination_images WHERE destination_id = ? ORDER BY is_primary DESC, created_at DESC");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    
    echo json_encode($images);
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>