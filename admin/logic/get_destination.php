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
        $destination['highlights'] = json_decode($destination['highlights'] ?? '[]', true) ?: [];
        $destination['activities'] = json_decode($destination['activities'] ?? '[]', true) ?: [];
        $destination['tips'] = json_decode($destination['tips'] ?? '[]', true) ?: [];
        $destination['nearby_attractions'] = json_decode($destination['nearby_attractions'] ?? '[]', true) ?: [];
        
        // Set default values for missing fields
        $destination['detailed_description'] = $destination['detailed_description'] ?? '';
        
        echo json_encode($destination);
    } else {
        echo json_encode(['error' => 'Destination not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>