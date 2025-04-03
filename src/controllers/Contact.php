<?php
session_start();
include '../includes/db.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<p style="color: red;">Please log in to send a message.</p>';
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $message = trim(htmlspecialchars($_POST['message']));
    $sender_id = $_SESSION['user_id']; // Get logged-in user ID from session

    if (!empty($message)) {
        try {
            if (!$conn) {
                throw new Exception("Database connection failed: " . mysqli_connect_error());
            }

            // Check if admin role exists, if not, create one
            $checkAdmin = $conn->query("SELECT * FROM users WHERE role = 'admin'");
            if ($checkAdmin->num_rows == 0) {
                $conn->query("INSERT INTO users (username, role) VALUES ('admin', 'admin')");
            }

            // Fetch admin ID dynamically
            $adminQuery = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
            if ($adminQuery->num_rows == 0) {
                throw new Exception("Admin user not found.");
            }
            $adminRow = $adminQuery->fetch_assoc();
            $receiver_id = $adminRow['id'];

            // Check if table exists
            $tableCheck = $conn->query("SHOW TABLES LIKE 'messages'");
            if ($tableCheck->num_rows == 0) {
                throw new Exception("Table 'messages' does not exist.");
            }

            $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }
            $stmt->bind_param("iis", $sender_id, $receiver_id, $message);

            if ($stmt->execute()) {
                // Show success message
                echo '<p style="color: green;">Message sent successfully! We will get back to you shortly.</p>';
            } else {
                throw new Exception("Database error during execution: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            echo '<p style="color: red;">Failed to send message: ' . $e->getMessage() . '</p>';
        }
    } else {
        echo '<p style="color: red;">Please enter a message before sending.</p>';
    }
}
?>

<section class="page-content">
    <h2>Contact Us</h2>
    <p>Have any questions? Reach out to us!</p>
    <form action="" method="post" class="contact-form">
        <label for="message">Your Message:</label>
        <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>

        <button type="submit">Send Message</button>
    </form>
</section>

<style>
    .contact-form {
        display: flex;
        flex-direction: column;
        max-width: 500px;
        margin: 0 auto;
        gap: 10px;
    }

    textarea {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 16px;
        width: 100%;
        min-height: 100px;
    }

    button {
        padding: 10px;
        border-radius: 5px;
        background-color: #28a745;
        color: white;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background-color: #218838;
    }
</style>
