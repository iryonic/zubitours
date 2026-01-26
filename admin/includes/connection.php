<?php

// Environment Setup (Database & Base URL)
$protocol = 'http';
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) {
    $protocol = 'https';
} elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $protocol = 'https';
}
$host_name = $_SERVER['HTTP_HOST'] ?? 'zubitours.com';

if ($host_name == 'localhost' || $host_name == '127.0.0.1' || strpos($host_name, '192.168.') !== false || strpos($host_name, '.local') !== false) {
    // Local Environment
    $host = 'localhost';
    $dbname = 'travel_db';
    $username = 'root';
    $password = '';
    
    if (!defined('BASE_URL')) {
        // Automatically detect the correct local path
        $path = (strpos($_SERVER['REQUEST_URI'], '/zubitours/') !== false) ? '/zubitours/' : '/';
        define('BASE_URL', $protocol . '://' . $host_name . $path);
    }
} else {
    // Production Environment
    $host = 'localhost';
    $dbname = 'u255290550_zubitours';
    $username = 'u255290550_zubitours';
    $password = 'Zubi@1234#';
    
    if (!defined('BASE_URL')) {
        define('BASE_URL', $protocol . '://zubitours.com/');
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
