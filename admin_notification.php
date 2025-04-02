<?php
// Include the database connection file
include('../includes/db_connect.php'); // Use your actual path to db_connect.php

// Query to count unread messages
$query = "SELECT COUNT(*) AS unread_count FROM messages WHERE status = 'unread'";
$result = $conn->query($query);

// Check if the query was successful
if ($result) {
    $row = $result->fetch_assoc();
    $unread_count = $row['unread_count'];

    // Display the notification message
    if ($unread_count > 0) {
        echo "<div style='padding: 10px; background-color: #ff9800; color: white; border-radius: 5px;'>
                <strong>New Messages:</strong> You have <strong>$unread_count</strong> unread message(s)!
              </div>";
    } else {
        echo "<div style='padding: 10px; background-color: #4caf50; color: white; border-radius: 5px;'>
                No new messages.
              </div>";
    }
} else {
    echo "Error retrieving messages: " . $conn->error;
}
?>