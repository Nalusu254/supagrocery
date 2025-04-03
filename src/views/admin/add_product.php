<?php
include '../includes/db.php';
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    // Correct SQL statement based on your table structure
    $sql = "INSERT INTO products (name, price, stock_quantity, category) VALUES (?, ?, ?, ?)"; // ✅ Use correct column names

    $stmt = $conn->prepare($sql);

    if ($stmt) { // ✅ Ensure prepare() was successful
        $stmt->bind_param("sdss", $product_name, $price, $stock, $category);
        if ($stmt->execute()) {
            $success = "Product added successfully!";
        } else {
            $error = "Failed to add product: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Query preparation failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            background: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #218838;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Product</h2>

        <?php if (!empty($success)) { echo "<p class='success'>$success</p>"; } ?>
        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>

        <form method="POST">
            <label>Product Name:</label>
            <input type="text" name="product_name" required>

            <label>Price (Ksh):</label>
            <input type="number" name="price" step="0.01" required>

            <label>Stock:</label>
            <input type="number" name="stock" required>

            <label>Category:</label>
            <input type="text" name="category" required>

            <button type="submit">➕ Add Product</button>
        </form>

        <br>
        <a href="manage_products.php">🔙 Back to Manage Products</a>
    </div>
</body>
</html>