<?php
// Define categorized products
$categories = [
    "Fruits" => [
        ["name" => "Apples", "price" => 150, "image" => "apples.jpeg"],
        ["name" => "Banana", "price" => 50, "image" => "banana.jpeg"],
        ["name" => "Grape", "price" => 250, "image" => "grape.jpeg"],
        ["name" => "Mango", "price" => 100, "image" => "mango.jpeg"],
        ["name" => "Oranges", "price" => 120, "image" => "oranges.jpeg"],
        ["name" => "Strawberries", "price" => 300, "image" => "strawberries.jpeg"],
        ["name" => "Watermelon", "price" => 250, "image" => "watermelon.jpeg"],
    ],
    "Vegetables" => [
        ["name" => "Cabbages", "price" => 80, "image" => "cabbages.jpeg"],
        ["name" => "Carrots", "price" => 60, "image" => "carrots.jpeg"],
        ["name" => "Cauliflower", "price" => 150, "image" => "cauliflower.jpeg"],
        ["name" => "Lettuce", "price" => 90, "image" => "lettuce.jpeg"],
        ["name" => "Onions", "price" => 60, "image" => "onions.jpeg"],
        ["name" => "Spinach", "price" => 50, "image" => "spinach.jpeg"],
        ["name" => "Tomato", "price" => 120, "image" => "tomato.jpeg"],
        ["name" => "Potatoes", "price" => 90, "image" => "potatoes.jpeg"],
    ],
    "Dairy Products" => [
        ["name" => "Cheese", "price" => 300, "image" => "cheese.jpeg"],
        ["name" => "Creek Yoghurt", "price" => 250, "image" => "creek yoghurt.jpeg"],
        ["name" => "Milk", "price" => 70, "image" => "milk.jpeg"],
        ["name" => "Semi-Skimmed Milk", "price" => 90, "image" => "semi-skimmed.jpeg"],
        ["name" => "Yoghurt Strawberry", "price" => 220, "image" => "yoghurt strawberry.jpeg"],
        ["name" => "Yoghurt Vanilla", "price" => 220, "image" => "yoghurt vanilla.jpeg"],
    ],
    "Meat & Poultry" => [
        ["name" => "Beef Meat", "price" => 450, "image" => "beef meat.jpeg"],
        ["name" => "Kienyeji Chicken", "price" => 700, "image" => "kienyeji chicken.jpeg"],
        ["name" => "Pork Meat", "price" => 550, "image" => "pork meat.jpeg"],
        ["name" => "Sausages", "price" => 400, "image" => "sausages.jpeg"],
        ["name" => "Eggs", "price" => 200, "image" => "eggs.jpeg"],
    ],
    "Grains & Cereals" => [
        ["name" => "Maize Flour", "price" => 120, "image" => "maize flour.jpeg"],
        ["name" => "Pasta", "price" => 200, "image" => "pasta.jpeg"],
        ["name" => "Rice (White)", "price" => 180, "image" => "white rice.jpeg"],
        ["name" => "Rice (Yellow)", "price" => 180, "image" => "yellow rice.jpeg"],
        ["name" => "Sunrice Rice", "price" => 350, "image" => "sunrice rice.jpeg"],
        ["name" => "White Oats", "price" => 180, "image" => "white oats.jpeg"],
        ["name" => "Wheat", "price" => 200, "image" => "wheat.jpeg"],
        ["name" => "Wheat Flour", "price" => 150, "image" => "wheat flour.jpeg"],
    ],
    "Beverages & Water" => [
        ["name" => "Dasani Water", "price" => 50, "image" => "dasani water.jpeg"],
        ["name" => "Drinking Water", "price" => 60, "image" => "drinking water.jpeg"],
        ["name" => "Water", "price" => 40, "image" => "water.jpeg"],
        ["name" => "Water Bottles", "price" => 100, "image" => "water bottles.jpeg"],
    ],
    "Cooking Essentials" => [
        ["name" => "Olive Oil", "price" => 500, "image" => "olive oil.jpeg"],
        ["name" => "Pika Oil", "price" => 450, "image" => "pika oil.jpeg"],
        ["name" => "Vegetable Oil", "price" => 400, "image" => "vegetables oil..>"],
    ],
    "Bakery" => [
        ["name" => "Breads", "price" => 100, "image" => "breads.jpeg"],
        ["name" => "Holiday Bread", "price" => 120, "image" => "holiday bread.jpeg"],
    ],
];

// Base URL for image path
$imageBaseURL = "http://supagrocery/assets/images/";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - SupaGrocery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .category {
            margin: 30px 0;
        }
        .category h2 {
            background: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }
        .products-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .product {
            border: 1px solid #ddd;
            padding: 15px;
            width: 200px;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            background: white;
        }
        .product img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        .order-btn {
            background: #28a745;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        .order-btn:hover {
            background: #218838;
        }
        .back-btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        .back-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <a href="customer_dashboard.php" class="back-btn">← Back to Dashboard</a> <!-- Back to Customer Dashboard -->
    <h1>Available Products</h1>
    <?php foreach ($categories as $category => $products): ?>
        <div class="category">
            <h2><?php echo $category; ?></h2>
            <div class="products-container">
                <?php foreach ($products as $product): ?>
                    <div class="product">
                    <img src="<?php echo $imageBaseURL . $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        <h3><?php echo $product['name']; ?></h3>
                        <p>Price: Ksh <?php echo number_format($product['price']); ?></p>
                        <div class="product">
                            <!-- Updated Order Form with Action Redirecting to place_order.php -->
                            <form action="place_order.php" method="POST">
                                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                                <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                                <label for="quantity">Quantity:</label>
                                <input type="number" name="quantity" value="1" min="1" required>
                                <button type="submit" class="order-btn">Place Order</button> <!-- Changed Text to Place Order -->
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>

