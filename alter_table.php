<?php
require_once 'admin/includes/connection.php';
$sql = "ALTER TABLE destinations 
        ADD COLUMN itinerary TEXT AFTER detailed_description, 
        ADD COLUMN inclusions TEXT AFTER itinerary, 
        ADD COLUMN exclusions TEXT AFTER inclusions, 
        ADD COLUMN faqs TEXT AFTER exclusions, 
        ADD COLUMN duration_days INT DEFAULT 0 AFTER destination_type, 
        ADD COLUMN price_per_person DECIMAL(10,2) DEFAULT 0 AFTER duration_days, 
        ADD COLUMN max_people INT DEFAULT 0 AFTER price_per_person, 
        ADD COLUMN accommodation_type VARCHAR(255) AFTER max_people";
if ($conn->query($sql)) {
    echo "Success";
} else {
    echo "Error: " . $conn->error;
}
?>
