<?php
session_start();
include("../config.php");

// Ensure the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    exit("Unauthorized access");
}

$admin_id = $_SESSION['user_id'];

// Fetch messages sent to the admin
$sql = "SELECT m.*, u.full_name 
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.receiver_id = ?
        ORDER BY m.sent_at ASC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error); // Debugging: Display SQL errors
}

$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

$output = "";

while ($row = $result->fetch_assoc()) {
    $sender = $row['full_name'];
    $message = htmlspecialchars($row['message']); // Prevent XSS
    $time = date("h:i A", strtotime($row['sent_at']));
    $output .= "<div class='message'><strong>$sender:</strong> $message <span class='time'>$time</span></div>";
}

// Mark messages as read
$update_sql = "UPDATE messages SET is_read = 1 WHERE receiver_id = ?";
$update_stmt = $conn->prepare($update_sql);

if ($update_stmt) {
    $update_stmt->bind_param("i", $admin_id);
    $update_stmt->execute();
} else {
    die("SQL Error: " . $conn->error); // Debugging: Display SQL errors
}

echo $output;
?>