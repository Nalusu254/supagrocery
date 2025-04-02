<?php
session_start();
include '../includes/db.php'; // Ensure database connection is included

if (!isset($_SESSION['user_id'])) {
    die("<p style='color: red;'>Error: User not logged in.</p>");
}

$order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;
$user_id = $_SESSION['user_id'];

// Build query
$sql = "SELECT id, total_price, status, order_date, delivery_agent_id, product_name, quantity 
        FROM orders WHERE id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("<p style='color: red;'>SQL Error: " . $conn->error . "</p>");
}

$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if order exists
if ($result->num_rows == 0) {
    die("<p style='color: red;'>Error: No order found.</p>");
}

// Fetch order details
$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 20px;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .back-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📜 Order Details</h2>
    <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
    <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
    <p><strong>Total Price: Ksh</strong> <?php echo number_format($order['total_price'], 2); ?></p>
    <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
    <p><strong>Courier:</strong> <?php echo empty($order['delivery_agent_id']) ? 'Pending' : $order['delivery_agent_id']; ?></p>

    <h3>🛒 Ordered Products</h3>
    <table>
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
        </tr>
        <tr>
            <td><?php echo $order['product_name']; ?></td>
            <td><?php echo $order['quantity']; ?></td>
        </tr>
    </table>

    <a href="my_orders.php" class="back-btn">🔙 Back to Orders</a>
</div>

</body>
</html>