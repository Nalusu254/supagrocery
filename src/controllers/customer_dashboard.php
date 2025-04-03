<?php
session_start(); // Start session
include '../includes/db.php'; // Include database connection

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php"); // Redirect unauthorized users
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <style>
        body {
            background: url('../assets/images/fruits.jpeg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            text-align: center;
            color: white;
        }
        .container {
            padding: 40px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            display: inline-block;
            margin-top: 10%;
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.2);
        }
        h1 {
            margin-bottom: 20px;
        }
        a {
            display: block;
            color: yellow;
            font-size: 18px;
            padding: 10px;
            background: rgba(255, 255, 0, 0.3);
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
            transition: 0.3s;
        }
        a:hover {
            background: rgba(255, 255, 0, 0.7);
            color: black;
        }
        .logout {
            color: white;
            background: red;
        }
        .logout:hover {
            background: darkred;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION['user_name']; ?> (Customer)!</h1>
        <a href="products.php">🛍️ View Products</a> <!-- Link to products.php -->
        <a href="products.php">🛒 Place Orders</a>
        <a href="my_orders.php">🛒 View Orders</a>
        <a href="track_order.php">🛒 Track Order</a> <!-- Link to views/track_order.php -->
        <a href="customer_view_messages.php">💬 View Messages</a>
        <a href="logout.php" class="logout">🚪 Logout</a>
        
    </div>
</body>
</html>