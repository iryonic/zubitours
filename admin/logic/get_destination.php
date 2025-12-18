<?php
session_start();
require_once '../includes/connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $result = $conn->query("SELECT * FROM destinations WHERE id = $id");
    if ($result->num_rows > 0) {
        $destination = $result->fetch_assoc();
        
        // Decode JSON fields
        $destination['best_seasons'] = json_decode($destination['best_seasons'], true) ?: [];
        $destination['highlights'] = json_decode($destination['highlights'], true) ?: [];
        $destination['activities'] = json_decode($destination['activities'], true) ?: [];
        $destination['tips'] = json_decode($destination['tips'], true) ?: [];
        $destination['nearby_attractions'] = json_decode($destination['nearby_attractions'], true) ?: [];
        
        header('Content-Type: application/json');
        echo json_encode($destination);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Destination not found']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Destination ID required']);
}
?>