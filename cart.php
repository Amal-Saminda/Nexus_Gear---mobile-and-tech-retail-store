<?php
session_start();
$conn = new mysqli("localhost", "root", "", "nexus_gear");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle quantity update
if (isset($_POST['update_qty']) && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $action = $_POST['update_qty'];

    // Initialize cart item with quantity 1 if it doesn't exist
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 1;
    }

    if ($action === 'increment') {
        $_SESSION['cart'][$product_id]++;
    } elseif ($action === 'decrement') {
        // Ensure quantity doesn't go below 1
        if ($_SESSION['cart'][$product_id] > 1) {
            $_SESSION['cart'][$product_id]--;
        }
    }

    header("Location: cart.php");
    exit;
}

// Handle direct quantity input
if (isset($_POST['set_qty']) && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    // Ensure quantity is at least 1
    if ($quantity < 1) {
        $quantity = 1;
    }
    $_SESSION['cart'][$product_id] = $quantity;
    
    header("Location: cart.php");
    exit;
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    unset($_SESSION['cart'][$remove_id]);
    header("Location: cart.php");
    exit;
}

// Handle clear cart
if (isset($_GET['clear_cart'])) {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;
}

// Fetch product details
$cart_items = $_SESSION['cart'] ?? [];
$products = [];
$total_price = 0;
$total_items = 0;

if (count($cart_items) > 0) {
    $ids = implode(',', array_keys($cart_items));
    $sql = "SELECT * FROM products WHERE id IN ($ids) ORDER BY FIELD(id, $ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $pid = $row['id'];
        // Get quantity from session, default to 1 if not set
        $qty = isset($cart_items[$pid]) ? $cart_items[$pid] : 1;
        $row['qty'] = $qty;
        $row['subtotal'] = $row['price'] * $qty;
        $products[] = $row;
        $total_price += $row['subtotal'];
        $total_items += $qty;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cart (<?= $total_items ?>) - Nexus Gear</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      background: #f5f5f5;
      line-height: 1.6;
    }
    
    header {
      background: linear-gradient(135deg, #0d47a1, #1565c0);
      padding: 1rem 2rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .navbar {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .logo {
      font-size: 1.8rem;
      font-weight: bold;
      color: white;
      flex-grow: 1;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    #nav-menu {
      list-style: none;
      display: flex;
      gap: 20px;
      margin: 0;
      padding: 0;
    }
    
    #nav-menu li a {
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      display: block;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }
    
    #nav-menu li a:hover {
      background-color: rgba(255,255,255,0.2);
    }
    
    .search-box input {
      padding: 8px 15px;
      border: none;
      border-radius: 25px;
      font-size: 14px;
      width: 200px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    main {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }

    .cart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .cart-header h1 {
      margin: 0;
      color: #0d47a1;
      font-size: 2rem;
    }

    .cart-stats {
      text-align: right;
      color: #666;
    }

    .clear-cart-btn {
      background: #f44336;
      color: white;
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      font-size: 0.9rem;
      margin-top: 10px;
      display: inline-block;
      transition: background-color 0.3s ease;
    }

    .clear-cart-btn:hover {
      background: #d32f2f;
    }

    .cart-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 25px;
      margin-bottom: 30px;
    }

    .product-card {
      background: white;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .product-image {
      height: 200px;
      width: 100%;
      object-fit: cover;
      margin-bottom: 15px;
      border-radius: 10px;
      border: 1px solid #eee;
    }

    .product-name {
      font-weight: bold;
      color: #0d47a1;
      font-size: 1.2rem;
      margin-bottom: 10px;
    }

    .product-price {
      color: #333;
      font-weight: bold;
      font-size: 1.1rem;
      margin-bottom: 10px;
    }

    .product-subtotal {
      color: #0d47a1;
      font-weight: bold;
      font-size: 1.1rem;
      margin-bottom: 15px;
    }

    .qty-controls {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 15px;
      justify-content: center;
    }

    .qty-btn {
      width: 35px;
      height: 35px;
      border: none;
      border-radius: 50%;
      background: #0d47a1;
      color: white;
      font-size: 1.2rem;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .qty-btn:hover {
      background: #1565c0;
      transform: scale(1.1);
    }

    .qty-btn:active {
      transform: scale(0.95);
    }

    .qty-input {
      width: 60px;
      text-align: center;
      padding: 8px;
      font-weight: bold;
      font-size: 1.1rem;
      border: 2px solid #ddd;
      border-radius: 8px;
      background: #f9f9f9;
    }

    .qty-input:focus {
      outline: none;
      border-color: #0d47a1;
      background: white;
    }

    .remove-btn {
      display: block;
      background: linear-gradient(135deg, #f44336, #d32f2f);
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      text-align: center;
      transition: all 0.3s ease;
      font-weight: bold;
    }

    .remove-btn:hover {
      background: linear-gradient(135deg, #d32f2f, #b71c1c);
      transform: translateY(-2px);
    }

    .checkout-section {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      text-align: center;
    }

    .total-summary {
      font-size: 1.3rem;
      margin-bottom: 20px;
      color: #333;
    }

    .total-amount {
      font-size: 2rem;
      font-weight: bold;
      color: #0d47a1;
      margin-bottom: 25px;
    }

    .checkout-btn {
      background: linear-gradient(135deg, #4caf50, #388e3c);
      color: white;
      padding: 15px 35px;
      font-size: 1.2rem;
      font-weight: bold;
      border: none;
      border-radius: 10px;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }

    .checkout-btn:hover {
      background: linear-gradient(135deg, #388e3c, #2e7d32);
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    }

    .empty-cart {
      text-align: center;
      padding: 60px 20px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .empty-cart h2 {
      color: #666;
      margin-bottom: 20px;
    }

    .shop-now-btn {
      background: linear-gradient(135deg, #0d47a1, #1565c0);
      color: white;
      padding: 12px 25px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .shop-now-btn:hover {
      background: linear-gradient(135deg, #1565c0, #1976d2);
      transform: translateY(-2px);
    }

    @media (max-width: 768px) {
      .cart-grid {
        grid-template-columns: 1fr;
      }
      
      .cart-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
      }
      
      .navbar {
        flex-direction: column;
        gap: 15px;
      }
      
      #nav-menu {
        flex-wrap: wrap;
        justify-content: center;
      }
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
    <?php if (count($products) === 0): ?>
      <div class="empty-cart">
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added any items to your cart yet.</p>
        <a href="products.php" class="shop-now-btn">Start Shopping</a>
      </div>
    <?php else: ?>
      <div class="cart-header">
        <h1>Your Cart</h1>
        <div class="cart-stats">
          <div><strong><?= $total_items ?></strong> item<?= $total_items !== 1 ? 's' : '' ?></div>
          <div>Subtotal: <strong>LKR <?= number_format($total_price, 2) ?></strong></div>
          <a href="cart.php?clear_cart=1" class="clear-cart-btn" onclick="return confirm('Clear entire cart?')">Clear Cart</a>
        </div>
      </div>

      <div class="cart-grid">
        <?php foreach ($products as $product): ?>
          <div class="product-card">
            <img class="product-image" src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
            <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
            <div class="product-price">Unit Price: LKR <?= number_format($product['price'], 2) ?></div>
            <div class="product-subtotal">Subtotal: LKR <?= number_format($product['subtotal'], 2) ?></div>

            <!-- Enhanced Quantity Controls -->
            <div class="qty-controls">
              <form method="POST" style="display: contents;">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
                <button type="submit" name="update_qty" value="decrement" class="qty-btn">-</button>
              </form>
              
              <form method="POST" style="display: contents;">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
                <input type="number" name="quantity" value="<?= htmlspecialchars($product['qty']) ?>" min="1" max="999" class="qty-input" 
                       onchange="if(this.value < 1) this.value = 1; this.form.submit();" />
                <input type="hidden" name="set_qty" value="1" />
              </form>
              
              <form method="POST" style="display: contents;">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
                <button type="submit" name="update_qty" value="increment" class="qty-btn">+</button>
              </form>
            </div>

            <!-- Remove Button -->
            <a class="remove-btn" href="cart.php?remove=<?= $product['id'] ?>" onclick="return confirm('Remove this item from cart?')">Remove Item</a>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="checkout-section">
        <div class="total-summary">
          Cart Summary: <?= $total_items ?> item<?= $total_items !== 1 ? 's' : '' ?>
        </div>
        <div class="total-amount">
          Total: LKR <?= number_format($total_price, 2) ?>
        </div>
        <a href="checkout.html" class="checkout-btn">Proceed to Checkout</a>
      </div>
    <?php endif; ?>
  </main>

  <script>
    // Enhanced quantity input validation
    document.querySelectorAll('.qty-input').forEach(input => {
      input.addEventListener('input', function() {
        let value = parseInt(this.value);
        if (isNaN(value) || value < 1) {
          this.value = 1;
        }
        if (value > 999) {
          this.value = 999;
        }
      });
      
      input.addEventListener('blur', function() {
        if (this.value === '' || parseInt(this.value) < 1) {
          this.value = 1;
          this.form.submit();
        }
      });
      
      // Handle keyboard events
      input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          if (this.value < 1) this.value = 1;
          this.form.submit();
        }
      });
    });

    // Prevent form submission on page load
    window.addEventListener('load', function() {
      document.querySelectorAll('.qty-input').forEach(input => {
        input.defaultValue = input.value;
      });
    });
  </script>

  
</body>
</html>