<?php
session_start();
require_once '../includes/connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $result = $conn->query("SELECT * FROM destination_images WHERE destination_id = $id ORDER BY is_primary DESC, created_at DESC");
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($images);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Destination ID required']);
}
?>