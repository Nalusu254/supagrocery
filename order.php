<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION["delivery_agent_id"])) {
    echo "<script>alert('Please login as admin first.'); window.location='admin_login.php';</script>";
    exit;
}

// ✅ Corrected SQL query: Use `user_id` instead of `customer_id`
$ordersQuery = mysqli_query($conn, "
    SELECT orders.*, users.full_name AS customer_name, users.phone 
    FROM orders 
    JOIN users ON orders.user_id = users.id 
    ORDER BY orders.order_date DESC
");

// ✅ Update dispatch status
if (isset($_POST["update_dispatch"])) {
    $order_id = mysqli_real_escape_string($conn, $_POST["order_id"]);
    $new_status = mysqli_real_escape_string($conn, $_POST["dispatch_status"]);

    $updateQuery = "UPDATE orders SET dispatch_status = '$new_status' WHERE id = '$order_id'";
    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Dispatch status updated!'); window.location='orders.php';</script>";
    } else {
        echo "<script>alert('❌ Error updating status.');</script>";
    }
}

// ✅ Delete order
if (isset($_GET["delete_order"])) {
    $order_id = mysqli_real_escape_string($conn, $_GET["delete_order"]);
    mysqli_query($conn, "DELETE FROM orders WHERE id = '$order_id'");
    echo "<script>alert('Order deleted successfully.'); window.location='orders.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>All Orders</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 90%; margin: auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); margin-top: 20px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: center; }
        th { background: #2c3e50; color: white; }
        .update-btn { background: green; color: white; padding: 8px; border: none; cursor: pointer; border-radius: 5px; }
        .delete-btn { background: red; color: white; padding: 8px; text-decoration: none; border-radius: 5px; }
        .details-btn { background: blue; color: white; padding: 8px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h2>All Orders</h2>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>Order Date</th>
            <th>Dispatch Status</th>
            <th>Update Status</th>
            <th>Actions</th>
        </tr>

        <?php while ($order = mysqli_fetch_assoc($ordersQuery)) { ?>
        <tr>
            <td><?= $order["id"] ?></td>
            <td><?= htmlspecialchars($order["customer_name"]) ?></td>
            <td><?= htmlspecialchars($order["phone"]) ?></td>
            <td><?= $order["order_date"] ?></td>
            <td><?= htmlspecialchars($order["dispatch_status"] ?? 'Pending') ?></td>

            <!-- ✅ Dispatch Status Update Form -->
            <td>
                <form method="post" action="orders.php">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="dispatch_status">
                        <option value="pending" <?= ($order["dispatch_status"] == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= ($order["dispatch_status"] == 'processing') ? 'selected' : '' ?>>Processing</option>
                        <option value="delivered" <?= ($order["dispatch_status"] == 'delivered') ? 'selected' : '' ?>>Delivered</option>
                    </select>
                    <button type="submit" name="update_dispatch" class="update-btn">Update</button>
                </form>
            </td>

            <!-- ✅ Actions -->
            <td>
                <a href="order_details.php?id=<?= $order['id'] ?>" class="details-btn">View</a>
                <a href="orders.php?delete_order=<?= $order['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>