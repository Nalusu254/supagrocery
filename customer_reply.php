<?php
session_start();
include('../includes/db_connect.php'); // Adjust the path as necessary

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];
$receiver_id = 1; // Default to admin

// Get the original message sender (admin) for the reply
if (isset($_POST['reply_to'])) {
    $message_id = $_POST['reply_to'];
    
    // Get the original message to identify the sender (admin)
    $query = "SELECT sender_id FROM messages WHERE id = $message_id";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $receiver_id = $row['sender_id']; // Set the receiver ID as the original sender
    } else {
        echo "Error: Message not found.";
        exit();
    }
}

// Handle message reply
if (isset($_POST['reply_message'])) {
    $reply_message = $_POST['reply_message'];
    $insertSql = "INSERT INTO messages (sender_id, receiver_id, message, sent_at) VALUES ($customer_id, $receiver_id, '$reply_message', NOW())";
    
    if ($conn->query($insertSql) === TRUE) {
        echo "Reply sent successfully.";
        header("Location: customer_view_messages.php");
        exit();
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
    <form method="POST" action="">
        <textarea name="reply_message" placeholder="Type your reply..." required></textarea>
        <br>
        <input type="hidden" name="reply_to" value="<?php echo $_POST['reply_to']; ?>">
        <button type="submit">Send Reply</button>
    </form>
    <a href="customer_view_messages.php">Back to Messages</a>
</body>
</html>
