<?php
session_start();
include '../includes/db.php'; // Database connection

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch orders
$order_sql = "SELECT orders.id, users.full_name AS customer_name, orders.total_price, orders.status, orders.agent_id 
              FROM orders 
              JOIN users ON orders.user_id = users.id
              ORDER BY orders.order_date DESC";
$orders = $conn->query($order_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; }
        .container { width: 90%; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #007BFF; color: white; }
        .btn { padding: 5px 10px; border-radius: 5px; background: green; color: white; border: none; cursor: pointer; }
        select { padding: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Orders</h2>
    <a href="admin_dashboard.php">⬅ Return to Dashboard</a>
    
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total Price (Ksh)</th>
            <th>Status</th>
            <th>Assigned Agent</th>
            <th>Actions</th>
        </tr>

        <?php while ($order = $orders->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $order['id']; ?></td>
            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
            <td>Ksh <?php echo number_format($order['total_price']); ?></td>
            <td><?php echo ucfirst($order['status']); ?></td>
            <td>
                <form method="POST" action="assign_agent.php">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <select name="agent_id" required>
    <option value="">Select Agent</option>
    <?php 
    $agent_sql = "SELECT id, full_name FROM users WHERE user_role = 'delivery_agent'";
    $agents = $conn->query($agent_sql);

    if ($agents->num_rows > 0) {
        while ($agent = $agents->fetch_assoc()) { ?>
            <option value="<?php echo $agent['id']; ?>" 
                <?php echo ($order['agent_id'] == $agent['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($agent['full_name']); ?>
            </option>
        <?php }
    } else {
        echo "<option disabled>No delivery agents found</option>";
    }
    ?>
</select>
                    <button type="submit" class="btn">Assign</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>