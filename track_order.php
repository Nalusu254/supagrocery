<?php
include '../includes/db.php'; // Ensure correct database connection
// Check if the user is logged in and is a customer

// If an order ID is provided via GET, track a specific order
$order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order</title>
</head>
<body>
    <h2>Track Your Orders</h2>

    <!-- Order Tracking Form -->
    <form method="GET" action="track_order.php">
        <label for="order_id">Enter Order ID:</label>
        <input type="text" name="order_id" id="order_id" required>
        <button type="submit">Track Order</button>
    </form>

    <?php
    if ($order_id) {
        // Fetch specific order
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $order = $result->fetch_assoc();
            echo "<h3>Order Details</h3>";
            echo "<table border='1'>
                    <tr><th>Order ID</th><th>Order Date</th><th>Status</th><th>Delivery Date</th></tr>
                    <tr>
                        <td>{$order['id']}</td>
                        <td>{$order['order_date']}</td>
                        <td>{$order['status']}</td>
                        <td>" . ($order['delivery_date'] ?? 'Pending') . "</td>
                    </tr>
                  </table>";
        } else {
            echo "<p style='color:red;'>⚠️ No order found with ID: " . htmlspecialchars($order_id) . "</p>";
        }
    }

    // Fetch all orders for display
    $sql = "SELECT * FROM orders ORDER BY order_date DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h3>All Orders</h3>";
        echo "<table border='1'>
                <tr><th>Order ID</th><th>Order Date</th><th>Status</th><th>Delivery Date</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['order_date']}</td>
                    <td>{$row['status']}</td>
                    <td>" . ($row['delivery_date'] ?? 'Pending') . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No orders found.</p>";
    }
    ?>

</body>
</html>
