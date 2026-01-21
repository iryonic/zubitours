<?php
require_once 'admin/includes/connection.php';
$result = $conn->query("SHOW COLUMNS FROM packages");
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
?>
