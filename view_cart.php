<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Your Shopping Cart</h2>
    
    <?php if (empty($cart)) { ?>
        <p>Your cart is empty.</p>
    <?php } else { ?>
        <table border="1">
            <tr>
                <th>Name</th>
                <th>Quantity</th>
                <th>Price (Ksh)</th>
                <th>Total (Ksh)</th>
                <th>Action</th>
            </tr>

            <?php
            $total_price = 0;
            foreach ($cart as $id => $quantity) {
                $sql = "SELECT * FROM products WHERE id = $id";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();

                $subtotal = $row['price'] * $quantity;
                $total_price += $subtotal;
            ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $quantity; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><?php echo $subtotal; ?></td>
                <td>
                    <a href="remove_from_cart.php?id=<?php echo $id; ?>">❌ Remove</a>
                </td>
            </tr>
            <?php } ?>
        </table>

        <h3>Total: Ksh <?php echo $total_price; ?></h3>
        <a href="checkout.php">✅ Proceed to Checkout</a>
    <?php } ?>
</body>
</html>
