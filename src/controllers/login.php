<?php
session_start();
include '../includes/db.php'; // Database connection

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

            // **Role-based redirection**
            if ($user['user_role'] == 'admin') {
                header("Location: ../views/admin_login.php"); // Admin verification
            } elseif ($user['user_role'] == 'delivery_agent') {
                header("Location: ../views/delivery_agent_login.php"); // Delivery agent verification
            } elseif ($user['user_role'] == 'customer') {
                header("Location: ../views/customer_login.php"); // Customer verification
                
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .container {
            width: 350px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        h2 {
            color: #333;
        }
        label {
            display: block;
            text-align: left;
            margin-top: 10px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>User Login</h2>
        <form method="POST" action="login.php">
            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="../views/sign_up.php">Sign_Up here</a></p>
    </div>
    <a href="../index.php" class="btn home-btn">⬅️ Return to Home</a>
</body>
</html>