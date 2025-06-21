<?php
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) die("DB Error");

$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
$categories = [];

while ($row = $result->fetch_assoc()) {
  $categories[] = $row;
}

echo json_encode($categories);
$conn->close();
?>
