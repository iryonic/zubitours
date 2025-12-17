<?php
require_once '../includes/connection.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM packages WHERE id = $id");
    
    if ($result->num_rows > 0) {
        $package = $result->fetch_assoc();
        
        // Decode JSON fields
        $package['highlights'] = json_decode($package['highlights'], true) ?: [];
        $package['inclusions'] = json_decode($package['inclusions'], true) ?: [];
        $package['exclusions'] = json_decode($package['exclusions'], true) ?: [];
        $package['faqs'] = json_decode($package['faqs'], true) ?: [];
        $package['itinerary'] = json_decode($package['itinerary'], true) ?: [];
        
        echo json_encode($package);
    } else {
        echo json_encode(['error' => 'Package not found']);
    }
}
