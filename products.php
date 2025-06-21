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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Products - Nexus Gear | Sri Lanka's Premier Tech Store</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      line-height: 1.6;
      color: #333;
      min-height: 100vh;
    }

    header {
      background: linear-gradient(135deg, #0d47a1, #1565c0);
      padding: 1rem 2rem;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .navbar {
      display: flex;
      align-items: center;
      gap: 20px;
      flex-wrap: wrap;
      max-width: 1200px;
      margin: 0 auto;
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
      gap: 15px;
      margin: 0;
      padding: 0;
    }

    #nav-menu li a {
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      display: block;
      border-radius: 8px;
      transition: all 0.3s ease;
      font-weight: 500;
    }

    #nav-menu li a:hover,
    #nav-menu li a.active {
      background-color: rgba(255,255,255,0.2);
      transform: translateY(-2px);
    }

    .search-form {
      display: flex;
      gap: 10px;
    }

    .search-box input {
      padding: 10px 15px;
      border: none;
      border-radius: 25px;
      font-size: 14px;
      width: 220px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      outline: none;
      transition: all 0.3s ease;
    }

    .search-box input:focus {
      width: 250px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .search-btn {
      background: white;
      color: #0d47a1;
      border: none;
      padding: 10px 15px;
      border-radius: 20px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .search-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .hero-section {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      text-align: center;
      padding: 60px 20px;
      position: relative;
      overflow: hidden;
    }

    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
      pointer-events: none;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      max-width: 800px;
      margin: 0 auto;
    }

    .hero-section h1 {
      font-size: 3rem;
      margin-bottom: 15px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
      animation: fadeInUp 1s ease-out;
    }

    .hero-section p {
      font-size: 1.2rem;
      opacity: 0.95;
      animation: fadeInUp 1s ease-out 0.3s both;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .filter-section {
      padding: 40px 0;
      text-align: center;
    }

    .filter-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }

    .filter-btn {
      padding: 12px 25px;
      background: white;
      color: #0d47a1;
      border: 2px solid #0d47a1;
      border-radius: 25px;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .filter-btn:hover,
    .filter-btn.active {
      background: #0d47a1;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(13, 71, 161, 0.3);
    }

    .products-section {
      padding: 40px 0 80px;
    }

    .products-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .products-header h2 {
      color: #0d47a1;
      font-size: 2.5rem;
      margin-bottom: 10px;
    }

    .products-count {
      color: #666;
      font-size: 1.1rem;
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
      margin-top: 40px;
    }

    .product-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: all 0.3s ease;
      position: relative;
    }

    .product-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .product-image {
      width: 100%;
      height: 220px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .product-card:hover .product-image {
      transform: scale(1.05);
    }

    .product-info {
      padding: 25px;
    }

    .product-category {
      color: #0d47a1;
      font-size: 0.85rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 8px;
    }

    .product-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: #333;
      margin-bottom: 10px;
      line-height: 1.3;
    }

    .product-description {
      color: #666;
      font-size: 0.95rem;
      line-height: 1.5;
      margin-bottom: 20px;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .product-price {
      font-size: 1.5rem;
      font-weight: bold;
      color: #0d47a1;
      margin-bottom: 20px;
    }

    .product-actions {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .add-to-cart-btn {
      flex: 1;
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .add-to-cart-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
    }

    .wishlist-btn {
      background: #f8f9fa;
      border: 2px solid #dee2e6;
      color: #6c757d;
      padding: 12px;
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1.2rem;
    }

    .wishlist-btn:hover {
      background: #ff6b6b;
      border-color: #ff6b6b;
      color: white;
    }

    .no-products {
      text-align: center;
      padding: 80px 20px;
      color: #666;
    }

    .no-products h3 {
      font-size: 2rem;
      margin-bottom: 15px;
      color: #333;
    }

    .no-products p {
      font-size: 1.1rem;
      margin-bottom: 30px;
    }

    .cta-section {
      background: linear-gradient(135deg, #0d47a1, #1565c0);
      color: white;
      text-align: center;
      padding: 60px 20px;
      margin: 60px 0 0;
      border-radius: 20px;
    }

    .cta-section h2 {
      font-size: 2.2rem;
      margin-bottom: 15px;
    }

    .cta-section p {
      font-size: 1.1rem;
      margin-bottom: 25px;
      opacity: 0.9;
    }

    .cta-btn {
      background: white;
      color: #0d47a1;
      padding: 15px 30px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      display: inline-block;
    }

    .cta-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    footer {
      background: #1a1a1a;
      color: white;
      text-align: center;
      padding: 40px 20px;
      margin-top: 60px;
    }

    .footer-links {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .footer-links a {
      color: #ccc;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .footer-links a:hover {
      color: #42a5f5;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 768px) {
      .hero-section h1 {
        font-size: 2.2rem;
      }
      
      .products-header h2 {
        font-size: 2rem;
      }
      
      .products-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }
      
      .filter-buttons {
        flex-direction: column;
        align-items: center;
      }
      
      .navbar {
        flex-direction: column;
        gap: 15px;
      }
      
      .search-form {
        width: 100%;
        justify-content: center;
      }
      
      .search-box input {
        width: 200px;
      }
    }
  </style>
</head>
<body>

 <header>
    <div class="navbar">
      <div class="logo">Nexus Gear</div>
      <ul id="nav-menu">
        <li><a href="Index.html">Home</a></li>
        <li class="dropdown">
          <a href="products.php">Products</a>
        </li>
        <li><a href="about.html">About Us</a></li>
        <li><a href="join.html">Join-Us</a></li>
        <li><a href="cart.php">Cart</a></li>


      <form method="GET" class="search-form">
        <div class="search-box">
      <input type="text" placeholder="🔍 Search products..." />
    </div>
        <button type="submit" class="search-btn">Search</button>
      </form>
    </div>
  </header>

  <section class="hero-section">
    <div class="hero-content">
      <h1>Our Products</h1>
      <p>Discover the latest technology at unbeatable prices</p>
    </div>
  </section>

  <section class="filter-section">
    <div class="container">
      <div class="filter-buttons">
        <a href="products.php" class="filter-btn <?= $filter == '' ? 'active' : '' ?>">All Products</a>
        <a href="products.php?category=Phones" class="filter-btn <?= $filter == 'Phones' ? 'active' : '' ?>">📱 Phones</a>
        <a href="products.php?category=Laptops" class="filter-btn <?= $filter == 'Laptops' ? 'active' : '' ?>">💻 Laptops</a>
        <a href="products.php?category=Tablets" class="filter-btn <?= $filter == 'Tablets' ? 'active' : '' ?>">📱 Tablets</a>
        <a href="products.php?category=Accessories" class="filter-btn <?= $filter == 'Accessories' ? 'active' : '' ?>">🎧 Accessories</a>
      </div>
    </div>
  </section>


      <?php if ($result->num_rows > 0): ?>
        <div class="products-grid">
          <?php while ($row = $result->fetch_assoc()): ?>
            <div class="product-card">
              <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="product-image">
              <div class="product-info">
                <div class="product-category"><?= htmlspecialchars($row['category']) ?></div>
                <h3 class="product-title"><?= htmlspecialchars($row['name']) ?></h3>
                <p class="product-description"><?= htmlspecialchars($row['description']) ?></p>
                <div class="product-price">LKR <?= number_format($row['price'], 2) ?></div>
                <div class="product-actions">
                  <form method="POST" style="flex: 1;">
                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="add_to_cart" class="add-to-cart-btn">
                      🛒 Add to Cart
                    </button>
                  </form>
                  <button class="wishlist-btn" title="Add to Wishlist">♡</button>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div class="no-products">
          <h3>No Products Found</h3>
          <p>We couldn't find any products matching your criteria.</p>
          <a href="products.php" class="cta-btn">Browse All Products</a>
        </div>
      <?php endif; ?>

      <div class="cta-section">
        <h2>Need Help Choosing?</h2>
        <p>Our tech experts are here to help you find the perfect device for your needs</p>
        <a href="contact.html" class="cta-btn">Contact Our Experts</a>
      </div>
    </div>
  </section>

  <footer>
    <div class="container">
      <div class="footer-links">
        <a href="index.html">Home</a>
        <a href="products.php">Products</a>
        <a href="about.html">About Us</a>
        <a href="join.html">Join-Us</a>
        <a href="cart.php">Cart</a>
      </div>
      <p>&copy; 2025 Nexus Gear — Sri Lanka's Most Trusted Tech Store</p>
      <p>Connecting you to the future, one device at a time</p> <br>
      <p>📍 Address:
<p>Nexus Gear Pvt Ltd</p>
<p></p>No. 45, Galle Road,</p>
Colombo 03,
Sri Lanka.
 </p>
 <p>
📧 Email:
 info@nexusgear.lk
      </p>
      <p>
☎️ Phone:
 +94 11 234 5678 /
 +94 77 123 4567 (WhatsApp available) </p>
    </div>
  </footer>

</body>
</html>