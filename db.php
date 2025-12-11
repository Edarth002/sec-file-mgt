<?php
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = ""; 
$DB_NAME = "sec_documents";

// $base_path = dirname($_SERVER['SCRIPT_NAME']);
// // Handle cases where the script is run from the root, resulting in just '.' or '\'
// if ($base_path == '\\' || $base_path == '.') { 
//     $base_path = ''; 
// }

// // Define the absolute path prefix for all resources and navigation
// // This will be something like '/sec-file-mgt/'
// define('BASE_URL', $base_path . '/');

define('BASE_URL', '/sec-file-mgt/');


// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>