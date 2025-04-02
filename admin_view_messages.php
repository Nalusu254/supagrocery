<?php
session_start();
include('../includes/db_connect.php'); // Adjust the path as necessary

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle message deletion
if (isset($_POST['delete_message'])) {
    $message_id = $_POST['delete_message'];
    $deleteSql = "DELETE FROM messages WHERE id = $message_id";
    $conn->query($deleteSql);
}

// Fetch all messages
$query = "SELECT * FROM messages ORDER BY sent_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Messages</title>
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            color: black;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .reply-button, .delete-button {
            background-color: #008CBA;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .reply-button:hover, .delete-button:hover {
            background-color: #005f73;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<a href="admin_dashboard.php">⬅ Return to Dashboard</a>
<h2 style="text-align: center;">Admin Messages</h2>
<table>
    <tr>
        <th>Sender ID</th>
        <th>Message</th>
        <th>Status</th>
        <th>Sent At</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['sender_id']; ?></td>
            <td><?php echo $row['message']; ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td><?php echo $row['sent_at']; ?></td>
            <td>
                <?php if ($row['sender_id'] != $_SESSION['user_id']) { ?>
                    <a href="admin_reply.php?message_id=<?php echo $row['id']; ?>" class="reply-button">Reply</a>
                <?php } ?>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="delete_message" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="delete-button">Delete</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>
</body>
</html>
