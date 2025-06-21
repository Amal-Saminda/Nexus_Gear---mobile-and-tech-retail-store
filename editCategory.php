

<!-- File: admin/editCategory.php -->
<?php
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = $_GET['id'] ?? 0;
$name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_name = $_POST['category_name'] ?? '';
  if ($new_name && $id) {
    $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $new_name, $id);
    $stmt->execute();
    header("Location: manageCategories.html");
  }
} else {
  $result = $conn->query("SELECT name FROM categories WHERE id = $id");
  if ($row = $result->fetch_assoc()) {
    $name = $row['name'];
  }
}
?>


<!DOCTYPE html>
<html>
<head><title>Edit Category</title></head>
<body>
  <h2>Edit Category</h2>
  <form method="POST">
    <input type="text" name="category_name" value="<?= htmlspecialchars($name) ?>" required>
    <button type="submit">Save Changes</button>
  </form>
</body>
</html>
