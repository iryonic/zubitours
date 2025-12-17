<?php
include '../includes/connection.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM car_rentals WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($car = $result->fetch_assoc()) {
        echo json_encode($car);
    } else {
        echo json_encode(['error' => 'Car not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>