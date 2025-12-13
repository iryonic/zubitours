<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {

    session_start();
    include '../includes/connection.php'; // DB connection

    // Collect and sanitize input
    $destination_id = isset($_POST['destination_id']) ? intval($_POST['destination_id']) : null;
    $destination_name = trim($_POST['destination_name'] ?? '');
    $region = trim($_POST['region'] ?? '');
    $description = trim($_POST['destination_description'] ?? '');
    $category = $_POST['category'];
    $status = $_POST['status'];
    $location = trim($_POST['location'] ?? '');
    $rating = floatval($_POST['rating'] ?? 0);

    // Validation
    $errors = [];

    if (empty($destination_name)) $errors[] = "Destination name is required.";
    if (empty($region)) $errors[] = "Region is required.";
    if (empty($description)) $errors[] = "Description is required.";
    if (empty($location)) $errors[] = "Location is required.";
    if ($rating < 0 || $rating > 5) $errors[] = "Rating must be between 0 and 5.";

    // Image validation
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../upload/destinations/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($imageFileType, $allowed_types)) {
            $errors[] = "Only JPG, JPEG, PNG, GIF, and WEBP files are allowed.";
        } else {
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false) {
                $errors[] = "Uploaded file is not a valid image.";
            }
        }
    } else {
        $errors[] = "Image is required.";
    }

    // Show validation errors
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<div style='color:red; margin-bottom:5px;'>$error</div>";
        }
        exit;
    }

    // Upload the image
    $filename = uniqid('dest_') . '.' . $imageFileType;
    $target_file = $target_dir . $filename;

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "<div style='color:red;'>Error uploading image.</div>";
        exit;
    }

    $image = $filename;

    // Prepare and insert into DB
    $stmt = $conn->prepare("INSERT INTO destinations (name, region, description, category, status ,location, rating, image) VALUES (?, ?, ?,? ,?,?,?, ?)");
    if (!$stmt) {
        echo "<div style='color:red;'>Database prepare failed: " . $con->error . "</div>";
        exit;
    }

    $stmt->bind_param("ssssssds", $destination_name, $region, $description,$category,$status, $location, $rating, $image);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Destination added successfully.";
        header("Location: ../pages/manage-destinations.php");
        
        exit;
    } else {
        echo "<div style='color:red;'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $con->close();

} else {
    header("Location: manage-destination.php");
    exit;
}
?>