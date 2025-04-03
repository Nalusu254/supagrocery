<?php
session_start();
include('../includes/db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['message_id'])) {
    $message_id = $_GET['message_id'];

    // Fetch the message details
    $query = "SELECT * FROM messages WHERE id = $message_id";
    $result = $conn->query($query);
    $message = $result->fetch_assoc();

    // Mark message as read
    $updateQuery = "UPDATE messages SET status = 'read' WHERE id = $message_id";
    $conn->query($updateQuery);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reply = $_POST['reply'];
    $receiver_id = $message['sender_id'];

    // Insert the reply into the messages table
    $insertQuery = "INSERT INTO messages (sender_id, receiver_id, message, status) VALUES ('{$_SESSION['user_id']}', '$receiver_id', '$reply', 'unread')";
    if ($conn->query($insertQuery)) {
        echo "Reply sent successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reply to Message</title>
</head>
<body>
    <h2>Reply to Message</h2>
    <p><strong>Message from User <?php echo $message['sender_id']; ?>:</strong> <?php echo $message['message']; ?></p>

    <form method="post">
        <label for="reply">Your Reply:</label><br>
        <textarea name="reply" rows="4" cols="50" required></textarea><br><br>
        <button type="submit">Send Reply</button>
    </form>

    <p><a href="admin_view_messages.php">Back to Messages</a></p>
</body>
</html>
