<?php

// local ENVIRONMENT  setup 


$servername = "localhost";
$usename = "root";
$password = "";
$dbname = "travel_db";


// Production ENVIRONMENT  setup 


// $servername = "localhost";
// $usename = "u255290550_zubitours";
// $password = "Zubi@1234#";
// $dbname = "u255290550_zubitours";



$conn = mysqli_connect($servername, $usename, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
