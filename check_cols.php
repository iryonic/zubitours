<?php
require_once 'admin/includes/connection.php';
$res = $conn->query("SHOW COLUMNS FROM destinations");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
?>
