
<?php
session_start();
include '../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
     // Collect and sanitize input
     $destination_id = intval($_POST['destination_id']);
     $destination_name = trim($_POST['destination_name'] ?? '');
     $region = trim($_POST['region'] ?? '');
     $description = trim($_POST['destination_description'] ?? '');
     $category = trim($_POST['category'] ?? '');
     $status = trim($_POST['status'] ?? 'active');
     $location = trim($_POST['location'] ?? '');
     $rating = floatval($_POST['rating'] ?? 0);

     // Validation
     $errors = [];

     if (empty($destination_name)) $errors[] = "Destination name is required.";
     if (empty($region)) $errors[] = "Region is required.";
     if (empty($description)) $errors[] = "Description is required.";
     if (empty($location)) $errors[] = "Location is required.";
     if ($rating < 0 || $rating > 5) $errors[] = "Rating must be between 0 and 5.";

     // Show validation errors
     if (!empty($errors)) {
          $_SESSION['message'] = [
               'type' => 'error',
               'text' => implode(' ', $errors)
          ];
          header("Location: ../pages/manage-destinations.php");
          exit;
     }

     // Handle image upload
     $image = null;
     if (!empty($_FILES['image']['name'])) {
          $target_dir = "../upload/destinations/";
          if (!file_exists($target_dir)) {
               mkdir($target_dir, 0777, true);
          }

          $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
          $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

          if (!in_array($imageFileType, $allowed_types)) {
               $_SESSION['message'] = [
                    'type' => 'error',
                    'text' => "Only JPG, JPEG, PNG, GIF, and WEBP files are allowed."
               ];
               header("Location: ../pages/manage-destinations.php");
               exit;
          } else {
               $check = getimagesize($_FILES["image"]["tmp_name"]);
               if ($check === false) {
                    $_SESSION['message'] = [
                         'type' => 'error',
                         'text' => "Uploaded file is not a valid image."
                    ];
                    header("Location: ../pages/manage-destinations.php");
                    exit;
               }
          }

          // Upload the new image
          $filename = uniqid('dest_') . '.' . $imageFileType;
          $target_file = $target_dir . $filename;

          if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
               $_SESSION['message'] = [
                    'type' => 'error',
                    'text' => "Error uploading image."
               ];
               header("Location: ../pages/manage-destinations.php");
               exit;
          }

          $image = $filename;

          // Delete old image
          $stmt = $con->prepare("SELECT image FROM destinations WHERE id = ?");
          $stmt->bind_param("i", $destination_id);
          $stmt->execute();
          $result = $stmt->get_result();
          $old_destination = $result->fetch_assoc();

          if ($old_destination && $old_destination['image']) {
               $old_image_path = "../upload/destinations/" . $old_destination['image'];
               if (file_exists($old_image_path)) {
                    unlink($old_image_path);
               }
          }
          $stmt->close();
     }

     // Prepare and update in DB
     if ($image) {
          // Update with new image
          $stmt = $con->prepare("UPDATE destinations SET name = ?, region = ?, description = ?, category = ?, status = ?, location = ?, rating = ?, image = ? WHERE id = ?");
          $stmt->bind_param("ssssssdsi", $destination_name, $region, $description, $category, $status, $location, $rating, $image, $destination_id);
     } else {
          // Update without changing image
          $stmt = $con->prepare("UPDATE destinations SET name = ?, region = ?, description = ?, category = ?, status = ?, location = ?, rating = ? WHERE id = ?");
          $stmt->bind_param("ssssssdi", $destination_name, $region, $description, $category, $status, $location, $rating, $destination_id);
     }

     if ($stmt->execute()) {
          $_SESSION['message'] = [
               'type' => 'success',
               'text' => "Destination updated successfully."
          ];
     } else {
          $_SESSION['message'] = [
               'type' => 'error',
               'text' => "Error updating destination: " . $stmt->error
          ];
     }

     $stmt->close();
     $con->close();

     header("Location: ../pages/manage-destinations.php");
     exit;
} else {
     header("Location: ../pages/manage-destinations.php");
     exit;
}
