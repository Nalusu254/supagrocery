<?php
session_start();
include '../includes/db.php'; // Include database connection

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $agent_id = $_POST['agent_id'];

    // Ensure valid input
    if (!empty($order_id) && !empty($agent_id)) {
        // Update the orders table to assign an agent
        $sql = "UPDATE orders SET agent_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $agent_id, $order_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Agent assigned successfully!";
        } else {
            $_SESSION['error'] = "Failed to assign agent.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Please select an agent.";
    }
}

$conn->close();
header("Location: admin_manage_orders.php"); // Redirect back to order management page
exit();