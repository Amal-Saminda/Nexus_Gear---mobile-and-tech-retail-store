<?php
session_start();
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }
    header("Location: cart.php");
    exit;
}

// Fetch product details for items in cart
$cart_items = $_SESSION['cart'] ?? [];
$products = [];
$total_price = 0;

if (count($cart_items) > 0) {
    $ids = implode(',', array_keys($cart_items));
    $sql = "SELECT * FROM products WHERE id IN ($ids) ORDER BY FIELD(id, $ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
        $total_price += $row['price'] * $cart_items[$row['id']];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Cart - Nexus Gear</title>
  <style>
    /* Navigation bar like home page */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      background: #f9f9f9;
    }
    header {
      background-color: #0d47a1;
      padding: 0.5rem 2rem;
    }
    .navbar {
      display: flex;
      align-items: center;
      gap: 20px;
      flex-wrap: wrap;
    }
    .logo {
      font-size: 1.5rem;
      font-weight: bold;
      color: white;
      flex-grow: 1;
    }
    #nav-menu {
      list-style: none;
      display: flex;
      align-items: center;
      margin: 0;
      padding: 0;
      gap: 20px;
    }
    #nav-menu li a {
      color: white;
      text-decoration: none;
      padding: 8px 10px;
      display: block;
      transition: background 0.3s ease;
    }
    #nav-menu li a:hover {
      background-color: #1565c0;
      border-radius: 5px;
    }
    .search-box {
      margin-left: auto;
    }
    .search-box input {
      padding: 6px 12px;
      border: none;
      border-radius: 20px;
      outline: none;
      font-size: 14px;
      width: 180px;
      transition: 0.3s ease;
    }
    .search-box input:focus {
      width: 220px;
    }

    main {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }

    h1 {
      color: #0d47a1;
      margin-bottom: 20px;
    }

    .cart-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }

    .product-card {
      background: white;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .product-image {
      height: 180px;
      object-fit: contain;
      margin-bottom: 10px;
    }

    .product-name {
      font-weight: bold;
      color: #0d47a1;
      margin-bottom: 6px;
      font-size: 1.1rem;
    }

    .product-category {
      font-size: 0.9rem;
      color: #555;
      margin-bottom: 8px;
    }

    .product-price {
      font-weight: bold;
      margin-bottom: 12px;
      color: #333;
      font-size: 1rem;
    }

    .quantity-info {
      font-size: 0.9rem;
      margin-bottom: 10px;
      color: #333;
    }

    .button-group {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    button, a.button {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      text-align: center;
      text-decoration: none;
      transition: background-color 0.3s ease;
      user-select: none;
    }

    button.view-details {
      background-color: #1565c0;
      color: white;
    }
    button.view-details:hover {
      background-color: #0d47a1;
    }

    button.add-to-cart {
      background-color: #28a745;
      color: white;
      cursor: not-allowed;
      opacity: 0.7;
    }

    a.remove-btn {
      background-color: #d32f2f;
      color: white;
    }
    a.remove-btn:hover {
      background-color: #b71c1c;
    }

    .empty-message {
      text-align: center;
      font-size: 1.2rem;
      color: #666;
      margin-top: 40px;
    }

    .checkout-section {
      margin-top: 30px;
      text-align: right;
    }
    .checkout-btn {
      background-color: #0d47a1;
      color: white;
      padding: 12px 25px;
      font-size: 1.1rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
    }
    .checkout-btn:hover {
      background-color: #1565c0;
    }

  </style>
</head>
<body>

  <header>
    <div class="navbar">
      <div class="logo">Nexus Gear</div>
      <ul id="nav-menu">
        <li><a href="index.html">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Join Us</a></li>
        <li class="search-box">
          <input type="text" id="searchInput" placeholder="🔍 Search products..." />
        </li>
      </ul>
    </div>
  </header>

  <main>
    <h1>Your Cart</h1>

    <?php if (count($products) === 0): ?>
      <p class="empty-message">Your cart is empty. <a href="products.php">Shop now</a></p>
    <?php else: ?>

    <div class="cart-grid" id="productGrid">

      <?php foreach ($products as $product): ?>
        <div class="product-card" data-name="<?= htmlspecialchars(strtolower($product['name'])) ?>">
          <img class="product-image" src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
          <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
          <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
          <div class="product-price">LKR <?= number_format($product['price'], 2) ?></div>
          <div class="quantity-info">Quantity: <?= intval($cart_items[$product['id']]) ?></div>

          <div class="button-group">
            <!-- View Details button -->
            <a href="product_details.php?id=<?= $product['id'] ?>" class="button view-details">View Details</a>

            <!-- Add to Cart button disabled on cart page -->
            <button class="add-to-cart" disabled>Added</button>

            <!-- Remove from Cart -->
            <a href="cart.php?remove=<?= $product['id'] ?>" class="remove-btn" onclick="return confirm('Remove this item?')">Remove</a>
          </div>
        </div>
      <?php endforeach; ?>

    </div>

    <div class="checkout-section">
      <strong>Total: LKR <?= number_format($total_price, 2) ?></strong>
      <br /><br />
      <a href="checkout.html" class="checkout-btn">Proceed to Checkout</a>
    </div>

    <?php endif; ?>
  </main>

  <script>
    // Search bar functionality - filters products by name on cart page
    const searchInput = document.getElementById('searchInput');
    const productGrid = document.getElementById('productGrid');
    searchInput.addEventListener('input', () => {
      const filter = searchInput.value.toLowerCase();
      const cards = productGrid.querySelectorAll('.product-card');
      cards.forEach(card => {
        const name = card.getAttribute('data-name');
        if (name.includes(filter)) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  </script>

</body>
</html>
