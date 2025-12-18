<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/connection.php';

$q = trim($_GET['q'] ?? '');
$results = [];

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("SELECT id, destination_name, location, short_description, (SELECT image_path FROM destination_images di WHERE di.destination_id = d.id AND di.is_primary = 1 LIMIT 1) AS image_path FROM destinations d WHERE is_active = 1 AND (destination_name LIKE ? OR location LIKE ? OR short_description LIKE ?) ORDER BY created_at DESC LIMIT 8");
    $stmt->bind_param('sss', $like, $like, $like);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $results[] = $row;
    }
}

echo json_encode($results);
