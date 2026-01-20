<?php
session_start();
require_once '../includes/connection.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Fetch highlights
    $h_stmt = $conn->prepare("SELECT * FROM destination_highlights WHERE destination_id = ? ORDER BY display_order ASC");
    $h_stmt->bind_param("i", $id);
    $h_stmt->execute();
    $highlights = $h_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Fetch activities
    $a_stmt = $conn->prepare("SELECT * FROM destination_activities WHERE destination_id = ?");
    $a_stmt->bind_param("i", $id);
    $a_stmt->execute();
    $activities = $a_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Fetch tips
    $t_stmt = $conn->prepare("SELECT * FROM destination_tips WHERE destination_id = ?");
    $t_stmt->bind_param("i", $id);
    $t_stmt->execute();
    $tips = $t_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Fetch nearby attractions
    $n_stmt = $conn->prepare("SELECT * FROM nearby_attractions WHERE destination_id = ?");
    $n_stmt->bind_param("i", $id);
    $n_stmt->execute();
    $nearby = $n_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'highlights' => $highlights,
        'activities' => $activities,
        'tips' => $tips,
        'nearby' => $nearby
    ]);
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>
