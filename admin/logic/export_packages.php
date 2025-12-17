<?php
require_once '../includes/connection.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="packages_export_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// Column headers
fputcsv($output, [
    'ID',
    'Package Name',
    'Type',
    'Duration (Days)',
    'Max People',
    'Accommodation Type',
    'Price per Person',
    'Rating',
    'Reviews Count',
    'Badge',
    'Is Featured',
    'Is Active',
    'Created At'
]);

// Data rows
$result = $conn->query("
    SELECT id, package_name, package_type, duration_days, max_people, 
           accommodation_type, price_per_person, rating, reviews_count, 
           badge, is_featured, is_active, created_at
    FROM packages
    ORDER BY id
");

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['package_name'],
        $row['package_type'],
        $row['duration_days'],
        $row['max_people'],
        $row['accommodation_type'],
        $row['price_per_person'],
        $row['rating'],
        $row['reviews_count'],
        $row['badge'],
        $row['is_featured'] ? 'Yes' : 'No',
        $row['is_active'] ? 'Yes' : 'No',
        $row['created_at']
    ]);
}

fclose($output);
exit;