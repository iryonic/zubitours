<?php
session_start();
require_once '../includes/connection.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$destination_id = intval($_POST['destination_id'] ?? 0);
$is_featured = intval($_POST['is_featured'] ?? 0);

if ($destination_id > 0) {
    $stmt = $conn->prepare("UPDATE destinations SET is_featured = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_featured, $destination_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid ID']);
}
?>
