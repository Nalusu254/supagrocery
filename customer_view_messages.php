<?php
session_start();
include('../includes/db_connect.php'); // Adjust the path as necessary

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

// Handle message deletion
if (isset($_POST['delete_message'])) {
    $message_id = $_POST['delete_message'];
    $deleteSql = "DELETE FROM messages WHERE id = $message_id";
    $conn->query($deleteSql);
}

// Fetch messages where the customer is the sender or receiver
$sql = "SELECT * FROM messages WHERE sender_id = $customer_id OR receiver_id = $customer_id ORDER BY sent_at ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Messages</title>
    <style>
        .chat-box {
            width: 60%;
            margin: 20px auto;
            padding: 10px;
            border-radius: 5px;
            background-color: #f1f1f1;
        }
        .message {
            margin: 5px 0;
            padding: 8px;
            border-radius: 5px;
            background-color: #ddd;
            position: relative;
        }
        .timestamp {
            font-size: 0.8em;
            color: gray;
            margin-top: 2px;
        }
        .btn {
            margin: 5px;
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #218838;
        }
        .edit-btn {
            background-color: #007bff;
        }
        .edit-btn:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .back-btn {
            background-color: #6c757d;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <h2>Customer Messages</h2>
    <a href="customer_dashboard.php" class="btn back-btn">⬅ Return to Dashboard</a>
    <div class="chat-box">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="message">
                    <p><?php echo htmlspecialchars($row['message']); ?>
                        <?php if ($row['sender_id'] == $customer_id): ?>
                            <form action="edit_message.php" method="POST" style="display:inline;">
                                <input type="hidden" name="message_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn edit-btn">Edit</button>
                            </form>
                        <?php else: ?>
                            <form action="customer_reply.php" method="POST" style="display:inline;">
                                <input type="hidden" name="reply_to" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn">Reply</button>
                            </form>
                        <?php endif; ?>
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="delete_message" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn delete-btn">Delete</button>
                        </form>
                    </p>
                    <div class="timestamp"><?php echo $row['sent_at']; ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No messages yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Update unread messages to read
$updateSql = "UPDATE messages SET is_read = 1 WHERE receiver_id = $customer_id AND is_read = 0";
$conn->query($updateSql);
?>
