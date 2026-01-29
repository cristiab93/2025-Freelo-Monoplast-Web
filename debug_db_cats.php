<?php
$host = "localhost";
$user = "cristianb";
$pass = "511xpWgxUR4icML4";
$db   = "monoplast";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Fail: " . mysqli_connect_error());

$res = mysqli_query($conn, "SELECT product_category, COUNT(*) as qty FROM products GROUP BY product_category");
echo "Categories in DB:\n";
while ($r = mysqli_fetch_assoc($res)) {
    echo "- '" . $r['product_category'] . "': " . $r['qty'] . "\n";
}
?>
