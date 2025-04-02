<?php
// Database connection (update credentials if necessary)
$conn = new mysqli("localhost", "root", "", "grocery_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product images from the database
$sql = "SELECT name, image FROM products";
$result = $conn->query($sql);

echo "<h2>Checking Missing Images...</h2>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imageFile = "../" . $row['image']; // Adjust path if needed

        if (!file_exists($imageFile)) {
            echo "<p style='color:red;'>❌ Missing: " . $row['name'] . " → " . $imageFile . "</p>";
        } else {
            echo "<p style='color:green;'>✅ Exists: " . $row['name'] . " → " . $imageFile . "</p>";
        }
    }
} else {
    echo "<p>No products found in the database.</p>";
}

$conn->close();
?>
<?php
$images = [
    "creek_yoghurt.jpeg",
    "yoghurt_strawberry.jpeg",
    "yoghurt_vanilla.jpeg",
    "beef_meat.jpeg",
    "pork_meat.jpeg",
    "kienyeji_chicken.jpeg",
    "maize_flour.jpeg",
    "white_rice.jpeg",
    "yellow_rice.jpeg",
    "dasani.jpeg",
    "water_bottles.jpeg",
    "olive_oil.jpeg",
    "vegetables_cooking_oil.jpeg"
];

foreach ($images as $image) {
    $path = realpath("../assets/images/" . $image);
    echo ($path ? "✅ Found: $image → $path" : "❌ Missing: $image") . "<br>";
}
?>