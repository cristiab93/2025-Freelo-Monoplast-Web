<?php
// debug_check_fks.php
// Check for foreign key constraints on the products table

$host = 'localhost';
$user = 'cristianb';
$pass = '511xpWgxUR4icML4';
$db   = 'monoplast';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die("Connection failed: " . $e->getMessage());
}

echo "<pre>";
echo "--- CHECKING CONSTRAINTS REFERENCING 'products' ---\n";

$sql = "SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE REFERENCED_TABLE_SCHEMA = '$db'
          AND REFERENCED_TABLE_NAME = 'products'";

$res = $conn->query($sql);

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "No tables reference 'products' via Foreign Keys.\n";
}

echo "\n--- CHECKING PRODUCTS TABLE STRUCTURE ---\n";
$res2 = $conn->query("DESCRIBE products");
while ($row = $res2->fetch_assoc()) {
    echo $row['Field'] . " | " . $row['Type'] . " | " . $row['Key'] . "\n";
}

echo "</pre>";
?>
