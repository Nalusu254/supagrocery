<?php
session_start();
include('../includes/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if order details are passed
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch order details from the database
    $query = "SELECT * FROM orders WHERE id = $order_id";
    $result = $conn->query($query);
    $order = $result->fetch_assoc();
} else {
    echo "Order not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
</head>
<body>
<h2>Order Confirmation</h2>

<?php if ($order) { ?>
    <p>Order ID: <?php echo $order['id']; ?></p>
    <p>Customer Name: <?php echo $order['customer_name']; ?></p>
    <p>Total Amount: Ksh <?php echo $order['total_amount']; ?></p>
    <p>Order Date: <?php echo $order['order_date']; ?></p>

    <button onclick="window.print();">Print Receipt</button>
<?php } else { ?>
    <p>Order details not available.</p>
<?php } ?>

<a href="../customer_dashboard.php">Back to Dashboard</a>
</body>
</html>
