<?php
// add_most_search_column.php
if (!isset($_SERVER['HTTP_HOST'])) $_SERVER['HTTP_HOST'] = 'localhost';
include __DIR__ . "/../conn/cfg.php";

$host = DBSERVERNAME;
$user = DBUSERNAME;
$pass = DBPASSWORD;
$db   = DBNAME;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset('utf8mb4');

echo "<pre>";
echo "<h2>Altering Table...</h2>\n";

try {
    $conn->query("ALTER TABLE products ADD COLUMN product_most_search INT NOT NULL DEFAULT 0");
    echo "Adding product_most_search to products table... OK\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column product_most_search already exists.\n";
    } else {
        echo "Error adding column: " . $e->getMessage() . "\n";
    }
}

echo "\n<h2>Check Schema</h2>";
$res = $conn->query("DESCRIBE products");
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . " | " . $row['Type'] . "\n";
}

echo "\n<h2>Done.</h2>";
echo "</pre>";
?>
