<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to DB
$conn = new mysqli("localhost", "root", "", "nexus_gear");

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Insert a test order
$sql = "INSERT INTO orders (customer_name, product_name, quantity, status)
        VALUES ('Amal Test', 'iPhone 15 Pro', 1, 'Pending')";

if ($conn->query($sql) === TRUE) {
    echo "✅ Test order inserted successfully!";
} else {
    echo "❌ Error: " . $conn->error;
}

$conn->close();
?>
