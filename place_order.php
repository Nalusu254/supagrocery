<?php
session_start();
include '../includes/db.php'; // Include database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get product details from the form
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Calculate total price
    $total_price = $price * $quantity;

    // Get customer information
    $user_id = $_SESSION['user_id'];

    // Prepare the SQL query
    $sql = "INSERT INTO orders (user_id, product_name, quantity, total_price, order_date) 
            VALUES (?, ?, ?, ?, NOW())";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing SQL query: " . $conn->error);
    }

    // Bind the parameters to the query
    $stmt->bind_param("isid", $user_id, $product_name, $quantity, $total_price);  // i = integer, s = string, d = decimal/float

    // Execute the query
    if ($stmt->execute()) {
        echo "<script>alert('Order placed successfully! You are being redirected to view your orders.'); window.location.href = 'my_orders.php';</script>";
    } else {
        echo "<script>alert('Error placing the order: " . $stmt->error . "'); window.location.href = 'product.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order - SupaGrocery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .order-container {
            width: 300px;
            margin: auto;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .order-container h2 {
            margin-bottom: 20px;
        }
        .order-details {
            margin-bottom: 20px;
        }
        .back-btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .back-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="order-container">
        <h2>Order Details</h2>
        <div class="order-details">
            <p><strong>Product Name:</strong> <?php echo htmlspecialchars($product_name); ?></p>
            <p><strong>Price:</strong> Ksh <?php echo number_format($price); ?></p>
            <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
            <p><strong>Total Price:</strong> Ksh <?php echo number_format($total_price); ?></p>
        </div>
        <a href="my_orders.php" class="back-btn">My Orders</a> <!-- Button to go to My orders page -->
    </div>
</body>
</html>