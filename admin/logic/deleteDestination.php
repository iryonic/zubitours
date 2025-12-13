<?php
session_start();
include '../includes/connection.php';

if (isset($_GET['id'])) {
     $id = intval($_GET['id']);

     // Optional: Get the image filename to delete the file
     $stmt = $conn->prepare("SELECT image FROM destinations WHERE id = ?");
     $stmt->bind_param("i", $id);
     $stmt->execute();
     $stmt->bind_result($image);
     $stmt->fetch();
     $stmt->close();

     if ($image && file_exists("../upload/destinations/$image")) {
          unlink("../upload/destinations/$image"); // delete the image file
     }

     // Delete the destination
     $stmt = $conn->prepare("DELETE FROM destinations WHERE id = ?");
     $stmt->bind_param("i", $id);

     if ($stmt->execute()) {
          $_SESSION['message'] = ['type' => 'success', 'text' => 'Destination removed successfully!'];
          header("Location: ../pages/manage-destinations.php");
     } else {
          $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: ' . 'failed to delete destination.'];
          header("Location: ../pages/manage-destinations.php");
     }

     $stmt->close();
     $conn->close();
} else {
     $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: Destination ID not provided.'];
     header("Location: ../pages/manage-destinations.php");
}
