<?php
session_start();
include '../includes/db.php'; // Ensure this path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = trim($_POST['user_role']); // Ensure this is being sent from the form

    // Check if required fields are set
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        die("<p style='color: red;'>All fields are required!</p>");
    }

    // Validate if passwords match
    if ($password !== $confirm_password) {
        die("<p style='color: red;'>Passwords do not match!</p>");
    }

    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_sql);
    
    if (!$stmt) {
        die("<p style='color: red;'>SQL error (check_sql): " . $conn->error . "</p>");
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("<p style='color: red;'>Email already registered!</p>");
    }
    $stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $sql = "INSERT INTO users (full_name, email, password, user_role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("<p style='color: red;'>SQL error (insert): " . $conn->error . "</p>");
    }

    $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Registration successful! You can now <a href='login.php'>Login</a>.</p>";
    } else {
        die("<p style='color: red;'>Error: " . $conn->error . "</p>");
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            background: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
    </style>

    <script>
        function validateForm() {
            var full_name = document.forms["sign_uprForm"]["full_name"].value;
            var email = document.forms["sign_upForm"]["email"].value;
            var phone = document.forms["sign_upForm"]["phone"].value;
            var password = document.forms["sign_upForm"]["password"].value;
            var confirm_password = document.forms["sign_upForm"]["confirm_password"].value;

            if (full_name == "" || email == "" || phone == "" || password == "" || confirm_password == "") {
                alert("All fields are required!");
                return false;
            }
            if (!/^\d{10}$/.test(phone)) {
                alert("Phone number must be exactly 10 digits!");
                return false;
            }
            if (password.length < 6) {
                alert("Password must be at least 6 characters long!");
                return false;
            }
            if (password !== confirm_password) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

    <div class="container">
        <h2>User Sign_Up</h2>
        <form name="sign_upForm" method="POST" action="" onsubmit="return validateForm()">
            <label>Full Name:</label>
            <input type="text" name="full_name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Phone:</label>
            <input type="text" name="phone" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>

            <label>User Role:</label>
            <select name="user_role" required>
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
                <option value="delivery_agent">Delivery Agent</option>
            </select>

            <button type="submit">Sign_up</button>
        </form>
        <p>Already Have An Account? <a href="login.php">Login</a></p>
    </div>

    

    <style>
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            margin: 5px;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
 <a href="../index.php" class="btn home-btn">⬅️ Return to Home</a>
</body>
</html>
