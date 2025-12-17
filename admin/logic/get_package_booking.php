<?php
require_once '../includes/connection.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("
        SELECT pb.*, p.package_name, p.duration_days, p.price_per_person
        FROM package_bookings pb 
        JOIN packages p ON pb.package_id = p.id 
        WHERE pb.id = $id
    ");
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Booking not found']);
    }
}