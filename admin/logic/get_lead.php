<?php
// logic/get_lead.php
require_once '../includes/connection.php';

if (isset($_GET['id'])) {
    $lead_id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM callback_leads WHERE id = $lead_id");
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Lead not found']);
    }
}
?>