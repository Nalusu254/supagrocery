<?php
session_start();
include '../includes/db.php'; // Database connection

// Ensure the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Check if product ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    // Delete the product from the database
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $product_id);
        if ($stmt->execute()) {
            header("Location: manage_products.php?success=Product deleted successfully");
            exit();
        } else {
            header("Location: manage_products.php?error=Failed to delete product");
            exit();
        }
        $stmt->close();
    } else {
        header("Location: manage_products.php?error=Query failed");
        exit();
    }
} else {
    header("Location: manage_products.php?error=Invalid product ID");
    exit();
}
?>