
<!-- File: admin/addCategory.php -->
<?php
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$name = $_POST['category_name'] ?? '';
if (!empty($name)) {
  $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
  $stmt->bind_param("s", $name);
  if ($stmt->execute()) {
    header("Location: manageCategories.php");

  } else {
    echo "Failed to add category.";
  }
  $stmt->close();
} else {
  echo "Category name is required.";
}
$conn->close();
?>