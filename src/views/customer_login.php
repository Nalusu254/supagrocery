<?php
session_start();
include '../includes/db.php'; // Include database connection

// Ensure only customers can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo "<script>alert('Access denied! Please login first.'); window.location='../index.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch customer details
    $sql = "SELECT id, full_name, password FROM users WHERE email = ? AND user_role = 'customer'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc(); // Fetch data into $customer

        // Verify password
        if (password_verify($password, $customer['password'])) { // FIXED THIS LINE
            $_SESSION['customer_verified'] = true; // Set customer verification session
            header("Location: ../views/customer_dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            echo "<p style='color: red; font-weight: bold;'>Invalid customer credentials!</p>";
        }
    } else {
        echo "<p style='color: red; font-weight: bold;'>Customer not found!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customer Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .container {
            width: 350px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: auto;
            margin-top: 50px;
        }
        h2 { color: #333; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-top: 5px; border-radius: 5px; }
        button { width: 100%; background: #007bff; color: white; padding: 10px; border: none; border-radius: 5px; margin-top: 15px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Customer Verification</h2>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Verify</button>
        </form>
    </div>

</body>
</html>










