<?php
require_once 'admin/includes/connection.php';

function get_insert_sql($table, $conn) {
    $res = $conn->query("SELECT * FROM $table");
    $inserts = [];
    while ($row = $res->fetch_assoc()) {
        $keys = array_keys($row);
        $values = array_map(function($val) use ($conn) {
            if ($val === null) return 'NULL';
            return "'" . $conn->real_escape_string($val) . "'";
        }, array_values($row));
        $inserts[] = "INSERT INTO `$table` (`" . implode("`, `", $keys) . "`) VALUES (" . implode(", ", $values) . ");";
    }
    return implode("\n", $inserts);
}

echo "-- Destinations Data Dump\n";
echo get_insert_sql('destinations', $conn) . "\n\n";

echo "-- Destination Images Data Dump\n";
echo get_insert_sql('destination_images', $conn) . "\n\n";

echo "-- Highlights Data Dump\n";
echo get_insert_sql('destination_highlights', $conn) . "\n\n";

echo "-- Activities Data Dump\n";
echo get_insert_sql('destination_activities', $conn) . "\n\n";

echo "-- Tips Data Dump\n";
echo get_insert_sql('destination_tips', $conn) . "\n\n";

echo "-- Nearby Attractions Data Dump\n";
echo get_insert_sql('nearby_attractions', $conn) . "\n\n";
?>
