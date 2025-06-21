<?php
session_start();

// Connect to database
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get customer data from checkout form
$customer_name    = $_POST['name'] ?? '';
$customer_address = $_POST['address'] ?? '';
$customer_phone   = $_POST['phone'] ?? '';

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    die("Cart is empty. Please add products before placing an order.");
}

foreach ($cart as $product_id => $quantity) {

    // Fetch product name from the products table
    $stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($product_name);
    $stmt->fetch();
    $stmt->close();

    // Insert order into orders table
    $stmt = $conn->prepare("INSERT INTO orders (product_id, product_name, quantity, customer_name, customer_address, customer_phone, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("isisss", $product_id, $product_name, $quantity, $customer_name, $customer_address, $customer_phone);
    $stmt->execute();
    $stmt->close();
}

// Clear the cart after placing order
unset($_SESSION['cart']);

// Redirect to thank you page
header("Location: thank_you.html");
exit;
?>
