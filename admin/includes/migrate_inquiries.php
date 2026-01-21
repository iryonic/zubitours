<?php
require_once 'connection.php';

$res = $conn->query("SHOW COLUMNS FROM contact_messages LIKE 'destination_id'");
if ($res->num_rows == 0) {
    if ($conn->query("ALTER TABLE contact_messages ADD COLUMN destination_id INT DEFAULT NULL AFTER id")) {
        echo "Column 'destination_id' added successfully.";
    } else {
        echo "Error adding column: " . $conn->error;
    }
} else {
    echo "Column 'destination_id' already exists.";
}

// Also check if we need to add other columns for the quick inquiry form
$form_fields = ['adults', 'children', 'travel_date'];
foreach ($form_fields as $field) {
    $res = $conn->query("SHOW COLUMNS FROM contact_messages LIKE '$field'");
    if ($res->num_rows == 0) {
        $type = ($field == 'travel_date') ? 'DATE' : 'INT';
        $conn->query("ALTER TABLE contact_messages ADD COLUMN $field $type DEFAULT NULL");
        echo "Column '$field' added.\n";
    }
}
?>
