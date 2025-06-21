<?php
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get ID
$id = $_GET['id'] ?? 0;

// Handle POST (form submission)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $newName = $_POST['category_name'];
  $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
  $stmt->bind_param("si", $newName, $id);
  if ($stmt->execute()) {
    header("Location: manageCategories.html");
    exit();
  } else {
    echo "Update failed.";
  }
  $stmt->close();
}

// Get existing category
$result = $conn->query("SELECT * FROM categories WHERE id = $id");
$category = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head><title>Edit Category</title></head>
<body>
  <h2>Edit Category</h2>
  <form method="POST">
    <input type="text" name="category_name" value="<?= htmlspecialchars($category['name']) ?>" required>
    <button type="submit">💾 Save Changes</button>
  </form>
</body>
</html>
