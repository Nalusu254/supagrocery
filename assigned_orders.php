<?php
session_start();
include '../includes/db.php'; // Include database connection

// Ensure only delivery agents can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'delivery_agent') {
    header("Location: ../login.php");
    exit();
}

$delivery_agent_id = $_SESSION['user_id'];

// Fetch assigned orders
$sql = "SELECT * FROM orders WHERE delivery_agent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $delivery_agent_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Assigned Orders</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <h2>My Assigned Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Address</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['customer_name']; ?></td>
                <td><?php echo $row['address']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                <a href="assigned_orders.php">📋 View Assigned Orders</a>
                    <a href="update_order_status.php?order_id=<?php echo $row['id']; ?>">Update Status</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>