<?php
session_start();
include '../includes/db.php'; // Database connection

// Ensure the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch orders with customer names
$sql = "SELECT orders.id, users.full_name AS customer_name, orders.total_price, orders.status, orders.order_date 
        FROM orders 
        JOIN users ON orders.user_id = users.id
        ORDER BY orders.order_date DESC";

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
    <title>View Orders</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; }
        .container { width: 90%; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: #007BFF; color: white; }
        .status { padding: 5px 10px; border-radius: 5px; display: inline-block; }
        .pending { background: orange; color: white; }
        .processing { background: blue; color: white; }
        .completed { background: green; color: white; }
        .cancelled { background: red; color: white; }
        .btn { display: inline-block; padding: 8px 12px; border-radius: 5px; text-decoration: none; color: white; cursor: pointer; }
        .update { background: green; }
        .return { background: gray; }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Orders</h2>
    <a href="admin_dashboard.php" class="btn return">⬅ Return to Dashboard</a>
    
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total Price (Ksh)</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php while ($order = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $order['id']; ?></td>
            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
            <td>Ksh <?php echo number_format($order['total_price']); ?></td>
            <td>
                <span class="status <?php echo strtolower($order['status']); ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </td>
            <td><?php echo $order['order_date']; ?></td>
            <td>
                <a href="admin_update_order.php?id=<?php echo $order['id']; ?>" class="btn update">✏ Update</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>