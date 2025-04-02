<?php
session_start();
include '../includes/db.php'; // Include database connection

// Check the request method (allowing both POST and GET)
if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
    // Try to get JSON data from the input
    $callbackData = file_get_contents('php://input');
    $decodedData = json_decode($callbackData, true);

    // If JSON data is not available, try to get POST or GET data directly
    if (!$decodedData) {
        $decodedData = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $_POST : $_GET;
    }

    // Log the received data for debugging
    file_put_contents("callback_log.txt", print_r($decodedData, true), FILE_APPEND);

    // Check if valid data is received
    if ($decodedData) {
        // Extract necessary payment details
        $order_id = $decodedData['order_id'] ?? null;
        $amount = $decodedData['amount'] ?? null;
        $status = $decodedData['status'] ?? null;
        $transaction_id = $decodedData['transaction_id'] ?? null;

        // Validate required fields
        if ($order_id && $amount && $status && $transaction_id) {
            // Update the order status in the database
            $sql = "UPDATE orders SET status = ?, transaction_id = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssi", $status, $transaction_id, $order_id);
                if ($stmt->execute()) {
                    http_response_code(200); // Success
                    echo json_encode(["message" => "Payment recorded successfully."]);
                } else {
                    http_response_code(500); // Server error
                    echo json_encode(["message" => "Failed to update order status."]);
                }
            } else {
                http_response_code(500); // Server error
                echo json_encode(["message" => "Database error: " . $conn->error]);
            }
        } else {
            http_response_code(400); // Bad request
            echo json_encode(["message" => "Invalid data received."]);
        }
    } else {
        http_response_code(400); // Bad request
        echo json_encode(["message" => "No data received."]);
    }
} else {
    http_response_code(405); // Method not allowed
    echo json_encode(["message" => "Invalid request method. Use POST or GET."]);
}
?>