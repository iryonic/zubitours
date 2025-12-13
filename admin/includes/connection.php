<?php
$servername = "localhost";
$usename = "root";
$password = "";
$dbname = "travel_db";

$conn = mysqli_connect($servername, $usename, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
