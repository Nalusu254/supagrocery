<?php
session_start();
include 'db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    echo "<script>alert('Please login to view your order details.'); window.location='login.php';</script>";
    exit;
}

$user_id = $_SESSION["user_id"];

// Fetch the most recent order for the user
$sql = "SELECT * FROM orders WHERE customer_id = '$user_id' ORDER BY order_date DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
} else {
    echo "<script>alert('No order found.'); window.location='dashboard.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Success</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; text-align: center; }
        .container { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        h2 { color: green; }
        p { font-size: 16px; color: #333; }
        .details { background: #eee; padding: 10px; margin-top: 10px; border-radius: 5px; text-align: left; }
        .btn { display: block; margin: 15px auto; padding: 10px 20px; text-decoration: none; color: white; background: green; border-radius: 5px; }
        .btn:hover { background: darkgreen; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🎉 Order Placed Successfully!</h2>
        <p>Thank you for your order. Your order details are below:</p>

        <div class="details">
            <p><strong>Order ID:</strong> <?= $order["id"] ?></p>
            <p><strong>Phone Number:</strong> <?= $order["phone_number"] ?></p>
            <p><strong>Total Amount:</strong> Ksh <?= number_format($order["total_amount"], 2) ?></p>
            <p><strong>Delivery County:</strong> <?= $order["county"] ?></p>
            <p><strong>Delivery Town:</strong> <?= $order["town"] ?></p>
            <p><strong>Courier Service:</strong> <?= $order["courier"] ?></p>
            <p><strong>Status:</strong> <?= $order["status"] ?></p>
            <p><strong>Dispatch Status:</strong> <?= isset($order["dispatch_status"]) ? $order["dispatch_status"] : "Pending" ?></p>
        </div>

        <a href="dashboard.php" class="btn">🛍 Continue Shopping</a>
        <a href="my_orders.php" class="btn">📦 View My Orders</a>
    </div>
</body>
</html>
