<?php
require_once '../includes/connection.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM faqs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($faq = $result->fetch_assoc()) {
        echo json_encode($faq);
    } else {
        echo json_encode(['error' => 'FAQ not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>