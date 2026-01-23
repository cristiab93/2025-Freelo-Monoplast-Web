<?php
// debug_schema.php
$host = 'localhost';
$user = 'cristianb';
$pass = '511xpWgxUR4icML4';
$db   = 'monoplast';

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset('utf8mb4');

echo "<pre>";

// 1. Check Table Info for products
echo "--- PRODUCTS TABLE SCHEMA ---\n";
$res = $conn->query("DESCRIBE products");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . " | " . $row['Type'] . "\n";
}

// 2. Check Product 14 and 15
echo "\n--- PRODUCT 14 and 15 ---\n";
$res = $conn->query("SELECT product_id, product_name, product_category, product_subcategory FROM products WHERE product_id IN (14, 15)");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}

// 3. Check Subcategories
echo "\n--- SUBCATEGORIES START ---\n";
$res = $conn->query("SELECT * FROM sub_categories LIMIT 5");
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
echo "</pre>";
?>
