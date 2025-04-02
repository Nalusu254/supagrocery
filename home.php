<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | SupaGrocery</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Header -->
    <header>
        <h1>SupaGrocery</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="views/products.php">Products</a>
            <a href="views/about.php">About</a>
            <a href="views/contact.php">Contact</a>
            <?php if (isset($_SESSION['user'])): ?>
                <a href="views/profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="views/login.php">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h2>WE DELIVER FRESH GROCERIES</h2>
        <p>Order fresh vegetables, dairy products, and more with just one click.</p>
        <a href="views/products.php" class="btn">SHOP NOW</a>
    </section>

    <!-- Featured Categories -->
    <section class="categories">
        <h3>SHOP BY CATEGORY</h3>
        <div class="category-list">
            <a href="views/products.php?category=Vegetables">🥦 Vegetables</a>
            <a href="views/products.php?category=Fruits">🍎 Fruits</a>
            <a href="views/products.php?category=Dairy">🥛 Dairy</a>
            <a href="views/products.php?category=Meat">🥩 Meat</a>
            <a href="views/products.php?category=Grains">🌾 Grains</a>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products">
        <h3>Popular Groceries</h3>
        <div class="product-list">
            <!-- Hardcoded example, later replace with PHP -->
            <div class="product-card">
                <img src="assets/images/apples.jpeg" alt="Apples">
                <h4>Fresh Apples</h4>
                <p>Ksh 200</p>
                <a href="views/products.php" class="btn">Order Now</a>
            </div>
            <div class="product-card">
                <img src="assets/images/milk.jpeg" alt="Milk">
                <h4>Fresh Milk</h4>
                <p>Ksh 150</p>
                <a href="views/products.php" class="btn">Place your order</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>Contact us at: care@supagrocery.com | 📞 0748677659</p>
        <p>&copy; <?php echo date('Y'); ?> SupaGrocery. All rights reserved.</p>
    </footer>

</body>
</html>