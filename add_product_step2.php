<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Step 2: DB connection
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Step 2: Validate form input and insert (no image yet)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($name && $category && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $category, $price, $description);

        if ($stmt->execute()) {
            echo "✅ Product added successfully without image.";
        } else {
            echo "❌ Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "⚠️ Please fill in all required fields correctly.";
    }
}

$conn->close();
?>
