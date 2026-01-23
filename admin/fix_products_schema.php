<?php
// fix_products_schema.php
$host = 'localhost';
$user = 'cristianb';
$pass = '511xpWgxUR4icML4';
$db   = 'monoplast';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset('utf8mb4');

echo "<pre>";
echo "<h2>1. Check Current Schema</h2>";
$res = $conn->query("DESCRIBE products");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . " | " . $row['Type'] . "\n";
}

echo "\n<h2>2. Altering Table...</h2>\n";

// Change product_category and product_subcategory to VARCHAR(100)
// We assume they might be keys or just strings. 100 chars should be safely enough for keywords.
// NOTE: We change the type. If there was integer data, it will be cast to string '1', '2' etc. which is fine for now,
// but our code expects keywords. (The code handles keywords mainly).

try {
    $conn->query("ALTER TABLE products CHANGE product_category product_category VARCHAR(100) NOT NULL DEFAULT ''");
    echo "Changing product_category to VARCHAR(100)... OK\n";
} catch (Exception $e) {
    echo "Error modifying product_category: " . $e->getMessage() . "\n";
}

try {
    $conn->query("ALTER TABLE products CHANGE product_subcategory product_subcategory VARCHAR(100) NOT NULL DEFAULT ''");
    echo "Changing product_subcategory to VARCHAR(100)... OK\n";
} catch (Exception $e) {
    echo "Error modifying product_subcategory: " . $e->getMessage() . "\n";
}

echo "\n<h2>3. Check New Schema</h2>";
$res = $conn->query("DESCRIBE products");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . " | " . $row['Type'] . "\n";
}

echo "\n<h2>4. Done.</h2>";
echo "</pre>";
?>
