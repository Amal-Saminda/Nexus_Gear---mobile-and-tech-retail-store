<?php
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add category
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['category_name'])) {
    $name = trim($_POST['category_name']);
    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_categories.php");
    exit;
}

// Delete category
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM categories WHERE id = $id");
    header("Location: manage_categories.php");
    exit;
}

// Fetch categories
$result = $conn->query("SELECT * FROM categories ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin - Manage Categories</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f2f2f2;
      margin: 0;
      padding: 20px;
    }
    h1 {
      color: #0d47a1;
    }
    form {
      margin-bottom: 20px;
      background: #fff;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    input[type="text"] {
      padding: 8px;
      width: 250px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      padding: 8px 16px;
      background-color: #0d47a1;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }
    th {
      background-color: #1565c0;
      color: white;
    }
    a.delete-btn {
      color: red;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <h1>Manage Categories</h1>

  <form method="POST">
    <label for="category_name">Add New Category:</label><br><br>
    <input type="text" name="category_name" id="category_name" required />
    <button type="submit">Add</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Category Name</th>
        <th>Created At</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td><a href="?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure to delete this category?')">Delete</a></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4">No categories found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
