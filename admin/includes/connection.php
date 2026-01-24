<?php

// Environment Setup (Database & Base URL)
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // Local Environment
    $host = 'localhost';
    $dbname = 'travel_db';
    $username = 'root';
    $password = '';
    
    if (!defined('BASE_URL')) {
        define('BASE_URL', 'http://localhost/zubitours/');
    }
} else {
    // Production Environment
    $host = 'localhost';
    $dbname = 'u255290550_zubitours';
    $username = 'u255290550_zubitours';
    $password = 'Zubi@1234#';
    
    if (!defined('BASE_URL')) {
        define('BASE_URL', 'http://zubitours.com/');
    }
}

// Create Connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


 if (!function_exists('createSlug')) {
     function createSlug($string) {
         $string = strtolower(trim($string));
         $string = preg_replace('/[^a-z0-9-]/', '-', $string);
         $string = preg_replace('/-+/', '-', $string);
         return trim($string, '-');
     }
 }
