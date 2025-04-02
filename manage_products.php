<?php
session_start();
include '../includes/db.php'; // Database connection

// Ensure the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch products from the database
$sql = "SELECT * FROM products ORDER BY created_at DESC"; // ✅ Corrected query
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; }
        .container { width: 80%; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        .product-container { display: flex; flex-wrap: wrap; justify-content: center; }
        .product { 
            width: 180px; margin: 10px; padding: 10px; background: #fff; 
            border: 1px solid #ddd; border-radius: 5px; text-align: center; 
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1); 
        }
        .product img { width: 100px; height: 100px; object-fit: cover; border-radius: 5px; }
        .btn { display: inline-block; padding: 5px 10px; margin: 5px; color: white; border: none; border-radius: 3px; text-decoration: none; cursor: pointer; }
        .edit { background: blue; }
        .delete { background: red; }
        .add-btn { background: green; font-size: 18px; padding: 10px; display: block; margin-bottom: 10px; }
        .dashboard-btn { background: orange; font-size: 16px; padding: 10px; margin-bottom: 10px; display: inline-block; }
    </style>
</head>
<body>

<div class="container">
<a href="../views/admin_dashboard.php" class="btn dashboard-btn">⬅️ Return to Dashboard</a>
    <h2>Manage Products</h2>
    <a href="add_product.php" class="btn add-btn">➕ Add New Product to Cat</a>

    <div class="product-container">
        <?php
        if ($result->num_rows > 0) {
            while ($product = $result->fetch_assoc()) { ?>
                <div class="product">
                    <img src="../<?php echo htmlspecialchars($product['image']); ?>" 
                         onerror="this.src='../assets/images/default.jpeg';">
                    <p><strong><?php echo htmlspecialchars($product['name']); ?></strong></p>
                    <p>Price: Ksh <?php echo number_format($product['price']); ?></p>
                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn edit">✏️ Edit</a>
                    <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this product?');">🗑 Delete</a>
                </div>
            <?php }
        } else {
            echo "<p>No products available.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>
