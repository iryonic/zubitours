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
    
    // Fetch destination
    $stmt = $conn->prepare("SELECT * FROM destinations WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $destination = $result->fetch_assoc();
        
        // Decode JSON fields
        $destination['best_seasons'] = json_decode($destination['best_seasons'], true) ?: [];
        
        echo json_encode($destination);
    } else {
        echo json_encode(['error' => 'Destination not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>