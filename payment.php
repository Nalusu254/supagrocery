<?php
session_start();
include('../includes/db_connect.php'); 

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

// Fetch pending orders for the customer
$query = "SELECT o.id, p.product_name, p.price, o.quantity 
          FROM orders o
          JOIN products p ON o.product_id = p.id
          WHERE o.customer_id = $customer_id AND o.status = 'pending'";
$result = $conn->query($query);

$total_price = 0;
$orders = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_price += $row['price'] * $row['quantity'];
        $orders[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
</head>
<body>
    <h2>Your Pending Orders</h2>
    <?php if (count($orders) > 0) { ?>
        <table border="1">
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
            <?php foreach ($orders as $order) { ?>
                <tr>
                    <td><?php echo $order['product_name']; ?></td>
                    <td>Ksh <?php echo $order['price']; ?></td>
                    <td><?php echo $order['quantity']; ?></td>
                    <td>Ksh <?php echo $order['price'] * $order['quantity']; ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="3"><strong>Total Amount</strong></td>
                <td><strong>Ksh <?php echo $total_price; ?></strong></td>
            </tr>
        </table>
        <form action="process_payment.php" method="POST">
            <input type="hidden" name="total_amount" value="<?php echo $total_price; ?>">
            <button type="submit">Pay Now</button>
        </form>
    <?php } else { ?>
        <p>No pending orders found.</p>
    <?php } ?>
</body>
</html>