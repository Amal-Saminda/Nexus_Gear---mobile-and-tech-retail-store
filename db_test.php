<?php
// Show all errors (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Step 1: Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "nexus_gear";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    echo "✅ Database connection successful!";
}

$conn->close();
?>
