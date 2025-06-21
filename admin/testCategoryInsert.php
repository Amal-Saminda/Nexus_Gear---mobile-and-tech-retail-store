<?php
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$name = "Test Category " . rand(1, 999);
$sql = "INSERT INTO categories (name) VALUES ('$name')";

if ($conn->query($sql)) {
  echo "✅ Inserted test category: $name";
} else {
  echo "❌ Error: " . $conn->error;
}

$conn->close();
?>
