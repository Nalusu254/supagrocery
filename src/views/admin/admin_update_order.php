<?php
session_start();
include '../includes/db.php'; // Database connection

// Ensure only the admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("<p style='color: red;'>Access Denied: Admins only.</p>");
}

// Ensure an order ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<p style='color: red;'>Error: Order ID is missing.</p>");
}

$order_id = intval($_GET['id']);

// Fetch the order details
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<p style='color: red;'>Error: Order not found.</p>");
}

$order = $result->fetch_assoc();

// Handle the update request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['status'])) {
        die("<p style='color: red;'>Error: Please select a status.</p>");
    }

    $status = $_POST['status'];

    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $status, $order_id);

    if ($update_stmt->execute()) {
        header("Location: admin_view_orders.php?message=Order updated successfully");
        exit();
    } else {
        die("<p style='color: red;'>Error updating order: " . $conn->error . "</p>");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }
        .container {
            width: 50%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
        }
        select {
            width: 100%;
            padding: 8px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            background: green;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Update Order Status</h2>
    <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
    <p><strong>Customer ID:</strong> <?php echo $order['user_id']; ?></p>
    <p><strong>Total Price:</strong> Ksh <?php echo number_format($order['total_price'], 2); ?></p>

    <form method="POST">
        <div class="form-group">
            <label for="status">Order Status:</label>
            <form method="POST" action="admin_update_order.php">
    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
    <select name="status" required>
        <option value="pending" <?php echo ($order['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
        <option value="processing" <?php echo ($order['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
        <option value="delivered" <?php echo ($order['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
    </select>
    
        </div>
        <button type="submit" class="btn">Update Order</button>
    </form>
</div>

</body>
</html>