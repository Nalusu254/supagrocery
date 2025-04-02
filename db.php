<?php
$servername = "localhost";  
$username = "root";  
$password = "";  // Leave empty if no password is set in XAMPP  
$database = "grocery_db";  // Ensure this is correct  

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>