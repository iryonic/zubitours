<?php

// local ENVIRONMENT  setup 



$host = 'localhost';
$dbname = 'travel_db';
$username = 'root';
$password = '';


// Production ENVIRONMENT  setup 




// $host = 'localhost';
// $dbname = 'u255290550_zubitours';
// $password = 'Zubi@1234#';
// $username = 'u255290550_zubitours';






 $conn = mysqli_connect($host, $username, $password, $dbname);

 if (!defined('BASE_URL')) {
     define('BASE_URL', '/zubitours/');
 }

 if (!function_exists('createSlug')) {
     function createSlug($string) {
         $string = strtolower(trim($string));
         $string = preg_replace('/[^a-z0-9-]/', '-', $string);
         $string = preg_replace('/-+/', '-', $string);
         return trim($string, '-');
     }
 }
