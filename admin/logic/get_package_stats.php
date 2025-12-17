<?php
require_once '../includes/connection.php';

header('Content-Type: application/json');

// Get package type distribution
$typeResult = $conn->query("
    SELECT package_type, COUNT(*) as count 
    FROM packages 
    WHERE is_active = 1 
    GROUP BY package_type
");

$typeLabels = [];
$typeData = [];
while ($row = $typeResult->fetch_assoc()) {
    $typeLabels[] = ucfirst($row['package_type']);
    $typeData[] = $row['count'];
}

// Get monthly bookings
$monthResult = $conn->query("
    SELECT DATE_FORMAT(booked_at, '%b') as month, COUNT(*) as count
    FROM package_bookings 
    WHERE booked_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(booked_at, '%Y-%m'), DATE_FORMAT(booked_at, '%b')
    ORDER BY MIN(booked_at)
");

$monthLabels = [];
$bookingData = [];
while ($row = $monthResult->fetch_assoc()) {
    $monthLabels[] = $row['month'];
    $bookingData[] = $row['count'];
}

// Get revenue by package
$revenueResult = $conn->query("
    SELECT p.package_name, SUM(pb.total_amount) as revenue
    FROM package_bookings pb
    JOIN packages p ON pb.package_id = p.id
    WHERE pb.booking_status IN ('confirmed', 'completed') 
    AND pb.payment_status = 'paid'
    GROUP BY p.id
    ORDER BY revenue DESC
    LIMIT 10
");

$packageLabels = [];
$revenueData = [];
while ($row = $revenueResult->fetch_assoc()) {
    $packageLabels[] = $row['package_name'];
    $revenueData[] = $row['revenue'];
}

echo json_encode([
    'typeLabels' => $typeLabels,
    'typeData' => $typeData,
    'monthLabels' => $monthLabels,
    'bookingData' => $bookingData,
    'packageLabels' => $packageLabels,
    'revenueData' => $revenueData
]);