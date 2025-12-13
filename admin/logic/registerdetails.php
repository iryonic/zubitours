<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = htmlspecialchars(ucfirst($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo " <script>alert('Invalid email format');
        focus: 'registerEmail';
        </script>";
    }

    $usernames = trim($_POST['usernames']);


    $pass = trim($_POST['password']);

    $hashedPass = password_hash($pass, PASSWORD_DEFAULT);

    include '../includes/connection.php';
    $stmt = $conn->prepare(
        "INSERT INTO admins (name, email,username, password) VALUES (?, ?, ?,?)"
    ) or die("Query failed: connection is not working");

    $stmt->bind_param("ssss", $name, $email, $usernames, $hashedPass);

    $stmt->execute();

    $stmt->close();

    header('Location: ../../exora/index.php');

    exit;
}
