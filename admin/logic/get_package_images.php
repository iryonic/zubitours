<?php
require_once '../includes/connection.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM package_images WHERE package_id = $id ORDER BY is_primary DESC, created_at DESC");
    
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    
    echo json_encode($images);
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>
