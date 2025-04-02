<?php
session_start();
include '../includes/db.php'; 

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request method. Ensure the form is submitting via POST.");
}

// Validate order ID and status
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    die("Error: Missing order_id or status.");
}

$order_id = intval($_POST['order_id']); 
$status = $_POST['status'];

// Check if order exists
$sql_check = "SELECT id FROM orders WHERE id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $order_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    die("Error: Order not found.");
}

// Update order status
$sql_update = "UPDATE orders SET status = ? WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("si", $status, $order_id);

if ($stmt_update->execute()) {
    header("Location: view_assigned_orders.php?message=Order updated successfully");
    exit();
} else {
    die("Error updating order: " . $conn->error);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order</title>
</head>
<body>

<h2>Update Order Status</h2>

<form method="POST" action="update_order_status.php">
    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
    <select name="status">
        <option value="pending" <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
        <option value="out_for_delivery" <?php echo ($row['status'] == 'out_for_delivery') ? 'selected' : ''; ?>>Out for Delivery</option>
        <option value="delivered">Delivered</option>
    </select>
    <button type="submit" class="btn">Update</button> <!-- Correct submission method -->
</form>

</body>
</html>
