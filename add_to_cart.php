<?php
session_start();
if (!isset($_SESSION["cart"])) $_SESSION["cart"] = [];

if (isset($_GET["product"]) && isset($_GET["price"])) {
    $_SESSION["cart"][] = ["name" => $_GET["product"], "price" => floatval($_GET["price"]), "quantity" => 1];
    echo "<script>alert('Added {$_GET["product"]} to cart!'); window.location.href='products.php';</script>";
}
?>