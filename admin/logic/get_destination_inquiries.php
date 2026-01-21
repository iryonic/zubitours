<?php
header('Content-Type: application/json');
require_once '../includes/connection.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $query = "SELECT cm.*, d.destination_name FROM contact_messages cm JOIN destinations d ON cm.destination_id = d.id WHERE cm.destination_id = ? ORDER BY cm.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
} else {
    $query = "SELECT cm.*, d.destination_name FROM contact_messages cm JOIN destinations d ON cm.destination_id = d.id ORDER BY cm.created_at DESC";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

$inquiries = [];
while ($row = $result->fetch_assoc()) {
    $inquiries[] = $row;
}

echo json_encode($inquiries);
?>
