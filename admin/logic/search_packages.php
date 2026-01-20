<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/connection.php';

$search = trim($_GET['search'] ?? '');
$duration = $_GET['duration'] ?? 'all';
$checkin = trim($_GET['checkin'] ?? '');
$travelers = isset($_GET['travelers']) ? max(0, intval($_GET['travelers'])) : 0;

$whereConditions = ["p.is_active = 1"];
$params = [];
$types = "";

if ($search !== '') {
    $whereConditions[] = "(p.package_name LIKE ? OR p.description LIKE ? OR p.package_type LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if ($duration !== 'all') {
    switch($duration) {
        case '3-5': $whereConditions[] = "p.duration_days BETWEEN 3 AND 5"; break;
        case '6-8': $whereConditions[] = "p.duration_days BETWEEN 6 AND 8"; break;
        case '9-12': $whereConditions[] = "p.duration_days BETWEEN 9 AND 12"; break;
        case '12+': $whereConditions[] = "p.duration_days >= 12"; break;
    }
}

if ($checkin !== '' && $travelers > 0) {
    $whereConditions[] = "(p.max_people - (
        SELECT COALESCE(SUM(pb.number_of_adults + pb.number_of_children), 0)
        FROM package_bookings pb
        WHERE pb.package_id = p.id
          AND pb.booking_status NOT IN ('cancelled', 'refunded')
          AND (pb.checkin_date < DATE_ADD(?, INTERVAL p.duration_days DAY) AND pb.checkout_date > ?)
    )) >= ?";
    $params[] = $checkin;
    $params[] = $checkin;
    $params[] = $travelers;
    $types .= 'ssi';
}

$whereClause = "WHERE " . implode(" AND ", $whereConditions);

$query = "
    SELECT p.*, pi.image_path 
    FROM packages p 
    LEFT JOIN package_images pi ON p.id = pi.package_id AND pi.is_primary = 1 
    $whereClause 
    ORDER BY p.is_featured DESC, p.created_at DESC 
    LIMIT 6";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$packages = [];
while ($row = $result->fetch_assoc()) {
    $packages[] = $row;
}

echo json_encode($packages);
