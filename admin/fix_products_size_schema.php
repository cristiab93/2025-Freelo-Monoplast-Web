<?php
// fix_products_size_schema.php
require_once("../_general.php");

echo "<pre>";
echo "<h2>1. Current Schema Check</h2>";
$res = mysqli_query($conn, "DESCRIBE products");
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " | " . $row['Type'] . "\n";
}

echo "\n<h2>2. Modifying product_size and product_subname to TEXT...</h2>\n";
try {
    mysqli_query($conn, "ALTER TABLE products MODIFY product_size TEXT NOT NULL");
    mysqli_query($conn, "ALTER TABLE products MODIFY product_subname TEXT NOT NULL");
    echo "Columns modified to TEXT... OK\n";
} catch (Exception $e) {
    echo "Error modifying column: " . $e->getMessage() . "\n";
}

echo "\n<h2>3. Updated Schema Check</h2>";
$res = mysqli_query($conn, "DESCRIBE products");
while ($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " | " . $row['Type'] . "\n";
}

echo "\n<h2>4. Done.</h2>";
echo "</pre>";
?>
