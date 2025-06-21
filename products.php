<?php
session_start();
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (!in_array($productId, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $productId;
    }
}

// Handle Filter
$filter = $_GET['category'] ?? '';
$sql = "SELECT * FROM products";
if ($filter != '') {
    $sql .= " WHERE category = '$filter'";
}
$sql .= " ORDER BY id DESC"; // Newest products first
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Products - Nexus Gear</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      margin: 0;
    }

    /* Menu bar */
    .navbar {
      background-color: #0d47a1;
      padding: 15px 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
      flex-wrap: wrap;
    }

    .logo {
      font-size: 24px;
      font-weight: bold;
    }

    .navbar ul {
      list-style: none;
      display: flex;
      gap: 20px;
      padding: 0;
      margin: 0;
    }

    .navbar li a {
      color: white;
      text-decoration: none;
      font-weight: bold;
    }

    .search-box {
      margin-top: 10px;
    }

    .search-box input {
      padding: 8px;
      border-radius: 4px;
      border: none;
      width: 250px;
    }

    .filter-buttons {
      text-align: center;
      margin: 30px 0 10px;
    }

    .filter-buttons form {
      display: inline-block;
      margin: 5px;
    }

    .filter-buttons button {
      padding: 10px 20px;
      background: #0d47a1;
      color: white;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .filter-buttons button:hover {
      background: #1565c0;
    }

    .products-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .product-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 15px;
      text-align: center;
    }

    .product-card img {
      max-width: 100%;
      max-height: 200px;
      border-radius: 8px;
      object-fit: cover;
    }

    .product-card h3 {
      margin: 10px 0 5px;
      font-size: 18px;
    }

    .product-card p {
      font-size: 14px;
      color: #555;
    }

    .product-card .price {
      font-weight: bold;
      margin: 10px 0;
      color: #0d47a1;
    }

    .add-to-cart {
      background: #28a745;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 10px;
    }

    .add-to-cart:hover {
      background: #218838;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="logo">Nexus Gear</div>
    <ul>
      <li><a href="index.html">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="#">About Us</a></li>
      <li><a href="#">Join Us</a></li>
      <li><a href="cart.php">View Cart</a></li>
    </ul>
    <div class="search-box">
      <input type="text" placeholder="🔍 Search products..." />
    </div>
  </div>

  <!-- Filter Buttons -->
  <div class="filter-buttons">
    <form method="get"><button type="submit" name="category" value="">All</button></form>
    <form method="get"><button type="submit" name="category" value="Phones">Phones</button></form>
    <form method="get"><button type="submit" name="category" value="Laptops">Laptops</button></form>
    <form method="get"><button type="submit" name="category" value="Tablets">Tablets</button></form>
    <form method="get"><button type="submit" name="category" value="Accessories">Accessories</button></form>
  </div>

  <!-- Products Grid -->
  <div class="products-container">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="product-card">
        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
        <h3><?= htmlspecialchars($row['name']) ?></h3>
        <p><?= htmlspecialchars($row['description']) ?></p>
        <div class="price">LKR <?= number_format($row['price'], 2) ?></div>
        <form method="POST">
          <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
          <button type="submit" name="add_to_cart" class="add-to-cart">🛒 Add to Cart</button>
        </form>
      </div>
    <?php endwhile; ?>
  </div>

</body>
</html>
