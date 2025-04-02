<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Remove item from cart
if (isset($_GET["remove"])) {
    $index = $_GET["remove"];
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

// Update quantity dynamically
if (isset($_POST['update_quantity'])) {
    $index = $_POST['index'];
    $newQuantity = intval($_POST['quantity']);

    if ($newQuantity > 0) {
        $_SESSION['cart'][$index]['quantity'] = $newQuantity;
    } else {
        unset($_SESSION['cart'][$index]); // Remove item if quantity is set to 0
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Proceed to checkout
if (isset($_POST["checkout"])) {
    echo "<script>alert('Proceeding to checkout...'); window.location.href='place_order.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; margin: 0; padding: 0; }
        .navbar { background: #007bff; padding: 15px; text-align: center; }
        .navbar a { color: white; text-decoration: none; padding: 10px 20px; font-size: 18px; }
        .container { width: 90%; margin: auto; padding: 20px; text-align: center; }
        .cart-table { width: 80%; margin: auto; background: white; border-radius: 8px; padding: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: center; }
        th { background: #007bff; color: white; }
        .remove-btn { background: red; color: white; padding: 5px 10px; border: none; cursor: pointer; border-radius: 5px; }
        .checkout-btn { background: #28a745; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 5px; }
        
        /* Quantity Controls */
        .quantity-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .quantity-btn {
            background: #ff9800;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        .quantity-input {
            width: 40px;
            text-align: center;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        /* Dynamic Price */
        .total-price { font-weight: bold; color: #d9534f; }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="dashboard.php">Shop</a>
        <a href="cart.php">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a>
    </div>

    <div class="container">
        <h2>Your Cart</h2>
        
        <?php if (!empty($_SESSION['cart'])) { ?>
            <div class="cart-table">
                <table>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price per kg</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($_SESSION['cart'] as $index => $item) { 
                        $totalPrice = $item["price"] * $item["quantity"];
                    ?>
                    <tr>
                    <img src="<?php echo $imageBaseURL . $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <td><?php echo $item["name"]; ?></td>
                        <td>Ksh <?php echo $item["price"]; ?></td>
                        
                        <!-- Quantity Update Form -->
                        <td>
                            <form method="post">
                                <div class="quantity-container">
                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(<?php echo $index; ?>, -1)">-</button>
                                    <input type="text" name="quantity" id="quantity-<?php echo $index; ?>" class="quantity-input" value="<?php echo $item["quantity"]; ?>" readonly>
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(<?php echo $index; ?>, 1)">+</button>
                                    <button type="submit" name="update_quantity" style="display: none;" id="update-<?php echo $index; ?>"></button>
                                </div>
                            </form>
                        </td>

                        <td>Ksh <span id="total-<?php echo $index; ?>"><?php echo number_format($totalPrice, 2); ?></span></td>
                        <td><a href="?remove=<?php echo $index; ?>" class="remove-btn">Remove</a></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <br>
            <form method="post">
                <button type="submit" name="checkout" class="checkout-btn">Proceed to Checkout</button>
            </form>
        <?php } else { ?>
            <p>Your cart is empty.</p>
        <?php } ?>
    </div>

<script>
    function updateQuantity(index, change) {
        var quantityInput = document.getElementById("quantity-" + index);
        var totalPrice = document.getElementById("total-" + index);
        var updateButton = document.getElementById("update-" + index);

        var pricePerKg = parseFloat(totalPrice.innerText) / parseInt(quantityInput.value);
        var quantity = parseInt(quantityInput.value) + change;

        if (quantity < 1) quantity = 1; // Prevent going below 1

        quantityInput.value = quantity;
        totalPrice.innerText = (quantity * pricePerKg).toFixed(2);

        updateButton.click(); // Automatically submit the form to update session
    }
</script>

</body>
</html>
<button onclick="goBack()">⬅ Back</button>
<button onclick="goForward()">Forward ➡</button>

<script>
function goBack() {
    window.history.back();
}

function goForward() {
    window.history.forward();
}
</script>

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
</style><button onclick="goBack()">⬅ Back</button>
<button onclick="goForward()">Forward ➡</button>

<script>
function goBack() {
    window.history.back();
}

function goForward() {
    window.history.forward();
}
</script>

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
