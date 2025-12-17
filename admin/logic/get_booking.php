<?php
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("
        SELECT cb.*, cr.car_name 
        FROM car_bookings cb 
        JOIN car_rentals cr ON cb.car_id = cr.id 
        WHERE cb.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($booking = $result->fetch_assoc()) {
        echo json_encode($booking);
    } else {
        echo json_encode(['error' => 'Booking not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>