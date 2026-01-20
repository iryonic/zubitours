<?php
session_start();
require_once '../includes/connection.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? '';
$destination_id = intval($_POST['destination_id'] ?? 0);

if (!$destination_id) {
    echo json_encode(['error' => 'Invalid destination ID']);
    exit();
}

switch ($action) {
    case 'add_highlight':
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $icon = $_POST['icon'] ?? 'ri-check-line';
        $order = intval($_POST['display_order'] ?? 0);
        
        $stmt = $conn->prepare("INSERT INTO destination_highlights (destination_id, title, description, icon, display_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $destination_id, $title, $description, $icon, $order);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
        break;
        
    case 'delete_highlight':
        $id = intval($_POST['id'] ?? 0);
        $conn->query("DELETE FROM destination_highlights WHERE id = $id AND destination_id = $destination_id");
        echo json_encode(['success' => true]);
        break;

    case 'add_activity':
        $name = $_POST['activity_name'] ?? '';
        $desc = $_POST['description'] ?? '';
        $level = $_POST['difficulty_level'] ?? 'easy';
        $duration = floatval($_POST['duration_hours'] ?? 0);
        $icon = $_POST['icon'] ?? 'ri-direction-line';
        
        $stmt = $conn->prepare("INSERT INTO destination_activities (destination_id, activity_name, description, icon, difficulty_level, duration_hours) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssd", $destination_id, $name, $desc, $icon, $level, $duration);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
        break;

    case 'delete_activity':
        $id = intval($_POST['id'] ?? 0);
        $conn->query("DELETE FROM destination_activities WHERE id = $id AND destination_id = $destination_id");
        echo json_encode(['success' => true]);
        break;

    case 'add_tip':
        $type = $_POST['tip_type'] ?? 'general';
        $title = $_POST['title'] ?? '';
        $desc = $_POST['description'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO destination_tips (destination_id, tip_type, title, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $destination_id, $type, $title, $desc);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
        break;

    case 'delete_tip':
        $id = intval($_POST['id'] ?? 0);
        $conn->query("DELETE FROM destination_tips WHERE id = $id AND destination_id = $destination_id");
        echo json_encode(['success' => true]);
        break;

    case 'add_nearby':
        $name = $_POST['attraction_name'] ?? '';
        $dist = floatval($_POST['distance_km'] ?? 0);
        $desc = $_POST['description'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO nearby_attractions (destination_id, attraction_name, distance_km, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isds", $destination_id, $name, $dist, $desc);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
        break;

    case 'delete_nearby':
        $id = intval($_POST['id'] ?? 0);
        $conn->query("DELETE FROM nearby_attractions WHERE id = $id AND destination_id = $destination_id");
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>
