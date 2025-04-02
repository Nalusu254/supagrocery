<?php
session_start();
include('db_connect.php');

// Assuming user_id is stored in session after login
$sender_id = $_SESSION['user_id'];
$receiver_id = 1;  // Assuming admin has ID 1
$message = mysqli_real_escape_string($conn, $_POST['message']);

if (!empty($message)) {
    $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ('$sender_id', '$receiver_id', '$message')";
    if (mysqli_query($conn, $query)) {
        echo "Message sent successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Message cannot be empty!";
}
?>
