<?php
session_start();
include '../includes/db.php'; // Ensure this is the correct path

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // Ensure ID is an integer

    // First, check if the user exists
    $check_user = $conn->query("SELECT * FROM users WHERE id = $user_id");
    
    if ($check_user->num_rows > 0) {
        // User exists, proceed with deletion
        $sql = "DELETE FROM users WHERE id = $user_id";
        if ($conn->query($sql) === TRUE) {
            header("Location: manage_users.php?msg=User+deleted+successfully");
            exit();
        } else {
            echo "Error deleting user: " . $conn->error;
        }
    } else {
        echo "User does not exist!";
    }
} else {
    echo "Invalid request!";
}
?>