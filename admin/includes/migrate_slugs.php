<?php
require_once 'connection.php';

function createSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

// Add slug column to destinations
$res = $conn->query("SHOW COLUMNS FROM destinations LIKE 'slug'");
if ($res->num_rows == 0) {
    $conn->query("ALTER TABLE destinations ADD COLUMN slug VARCHAR(255) UNIQUE AFTER destination_name");
    echo "Slug column added to destinations.\n";
}

// Add slug column to packages
$res = $conn->query("SHOW COLUMNS FROM packages LIKE 'slug'");
if ($res->num_rows == 0) {
    $conn->query("ALTER TABLE packages ADD COLUMN slug VARCHAR(255) UNIQUE AFTER package_name");
    echo "Slug column added to packages.\n";
}

// Populate slugs for destinations
$destinations = $conn->query("SELECT id, destination_name FROM destinations WHERE slug IS NULL OR slug = ''");
while ($row = $destinations->fetch_assoc()) {
    $slug = createSlug($row['destination_name']);
    // Ensure uniqueness
    $check = $conn->query("SELECT id FROM destinations WHERE slug = '$slug'");
    if ($check->num_rows > 0) {
        $slug .= '-' . $row['id'];
    }
    $conn->query("UPDATE destinations SET slug = '$slug' WHERE id = " . $row['id']);
}
echo "Destinations slugs populated.\n";

// Populate slugs for packages
$packages = $conn->query("SELECT id, package_name FROM packages WHERE slug IS NULL OR slug = ''");
while ($row = $packages->fetch_assoc()) {
    $slug = createSlug($row['package_name']);
    // Ensure uniqueness
    $check = $conn->query("SELECT id FROM packages WHERE slug = '$slug'");
    if ($check->num_rows > 0) {
        $slug .= '-' . $row['id'];
    }
    $conn->query("UPDATE packages SET slug = '$slug' WHERE id = " . $row['id']);
}
echo "Packages slugs populated.\n";
?>
