<?php
session_start();
require_once '../includes/connection.php';

// Redirect if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['id'])) {
        // Single deletion
        $id = intval($data['id']);
        $stmt = $conn->prepare("DELETE FROM package_bookings WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
    } elseif (isset($data['ids']) && is_array($data['ids'])) {
        // Bulk deletion
        $ids = array_map('intval', $data['ids']);
        if (empty($ids)) {
            echo json_encode(['error' => 'No IDs provided']);
            exit();
        }
        
        $id_list = implode(',', $ids);
        $sql = "DELETE FROM package_bookings WHERE id IN ($id_list)";
        
        if ($conn->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
    } else {
        echo json_encode(['error' => 'Invalid request']);
    }
} else {
    echo json_encode(['error' => 'Method not allowed']);
}
?>
