<?php
// Database Configuration
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hostel_finder_db"; // As seen in your phpMyAdmin screenshot

// Create Connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Global constants
define('BASE_URL', 'http://localhost/hostel_finder/');
