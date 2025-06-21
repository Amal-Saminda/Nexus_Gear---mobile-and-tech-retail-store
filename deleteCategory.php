

<!-- File: admin/deleteCategory.php -->
<?php
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = $_GET['id'] ?? 0;
if ($id) {
  $conn->query("DELETE FROM categories WHERE id = $id");
}
header("Location: manageCategories.html");
$conn->close();
?>