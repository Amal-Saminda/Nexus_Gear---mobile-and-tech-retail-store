<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Inputs
$name = $_POST['name'] ?? '';
$category = $_POST['category'] ?? '';
$price = $_POST['price'] ?? 0;
$description = $_POST['description'] ?? '';

$image = $_FILES['image']['name'];
$tmp_name = $_FILES['image']['tmp_name'];
$upload_dir = "uploads/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$target_path = $upload_dir . basename($image);

if (move_uploaded_file($tmp_name, $target_path)) {
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO products (name, category, price, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $name, $category, $price, $description, $image);

    if ($stmt->execute()) {
        echo "<h3>✅ Product added successfully!</h3>";
        echo "<a href='testAdd.html'>➕ Add Another</a>";
    } else {
        echo "❌ Database insert error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "❌ Failed to upload image.";
}

$conn->close();
?>
