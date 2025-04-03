<?php
session_start();
include '../includes/db.php';

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    echo "<script>alert('Please login to view your orders.'); window.location='login.php';</script>";
    exit;
}

$user_id = $_SESSION["user_id"];

// Handle order deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM orders WHERE id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    if ($delete_stmt) {
        $delete_stmt->bind_param("ii", $delete_id, $user_id);
        if ($delete_stmt->execute()) {
            echo "<script>alert('Order deleted successfully.'); window.location='my_orders.php';</script>";
        } else {
            echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
        }
    }
}

// Fetch orders for the logged-in user
$sql = "SELECT id, total_price, status, delivery_agent_id, product_name, quantity, paid FROM orders WHERE user_id = ? AND status = 'pending'";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
}

// Handle button clicks
if (isset($_POST['place_order'])) {
    header("Location: products.php");
    exit;
}

if (isset($_POST['pay_now'])) {
    header("Location: payment.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; text-align: center; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: #28a745; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .btn { padding: 5px 10px; background: green; color: white; text-decoration: none; border-radius: 5px; }
        .btn:hover { background: darkgreen; }
        .delete-btn { background: red; }
        .delete-btn:hover { background: darkred; }
        .pay-btn { background: blue; }
        .pay-btn:hover { background: darkblue; }
    </style>
</head>
<body>
    <div class="container">
    <a href="customer_dashboard.php" class="back-btn">← Back to Dashboard</a> <!-- Back to Customer Dashboard -->
        <h2>📦 My Orders</h2>
        <?php $total_amount = 0; ?>
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Amount (Ksh)</th>
                    <th>Status</th>
                    <th>Courier</th>
                    <th>Dispatch Status</th>
                    <th>Details</th>
                    <th>Delete</th>
                </tr>
                <?php while ($order = $result->fetch_assoc()): ?>
                    <?php $total_amount += $order["total_price"]; ?>
                    <tr>
                        <td><?= $order["id"] ?></td>
                        <td><?= htmlspecialchars($order["product_name"]) ?></td>
                        <td><?= $order["quantity"] ?></td>
                        <td><?= number_format($order["total_price"], 2) ?> Ksh</td>
                        <td><?= $order["status"] ?></td>
                        <td><?= isset($order["delivery_agent_id"]) ? $order["delivery_agent_id"] : "Pending" ?></td>
                        <td><?= ($order["status"] == "delivered") ? "Completed" : "Pending" ?></td>
                        <td><a href="order_details.php?order_id=<?= $order['id'] ?>" class="btn">View</a></td>
    </td>
                        <td><a href="my_orders.php?delete_id=<?= $order['id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a></td>
                    </tr>
                <?php endwhile; ?>
                <tr>
    <td colspan="3"><strong>Total Price:</strong></td>
    <td colspan="7"><strong><?= number_format($total_amount, 2) ?> Ksh</strong></td>
</tr>
</table>

<form method="POST" action="payment.php">
    <button type="submit" name="pay_now" class="btn pay-btn">Pay Now</button>
</form>
            <form method="POST">
                <button type="submit" name="place_order" class="btn">Place Order</button>
                <td>
            </form>
           
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
