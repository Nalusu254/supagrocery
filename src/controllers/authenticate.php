<?php
session_start();
include '../includes/db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from the database
    $sql = "SELECT id, full_name, password, user_role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role'] = $user['user_role']; // Store role in session

            // Check if a redirect parameter exists
            $redirect = $_GET['redirect'] ?? '';

            // Redirect based on role
            if ($user['user_role'] == 'admin') {
                header("Location: ../views/admin_login.php"); // Admins go to admin login first
            } else {
                header("Location: " . (!empty($redirect) ? $redirect : '../home.php')); // Redirect to previous page or home
            }
            exit();
        } else {
            echo "<p style='color: red; font-weight: bold;'>Invalid email or password!</p>";
        }
    } else {
        echo "<p style='color: red; font-weight: bold;'>User not found!</p>";
    }
}
?>
