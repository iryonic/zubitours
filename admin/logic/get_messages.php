<?php
require_once '../includes/connection.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($message = $result->fetch_assoc()) {
        echo json_encode($message);
    } else {
        echo json_encode(['error' => 'Message not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>