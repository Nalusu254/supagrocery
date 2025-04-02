<?php
$host = "localhost";
$user = "root"; // Default XAMPP user
$pass = ""; // Default XAMPP password is empty
$dbname = "grocery_db"; // Ensure this is your actual database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>