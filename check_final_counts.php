<?php
require_once("_general.php");
$res = mysqli_query($conn, "SELECT count(*) as total FROM p");
$row = mysqli_fetch_assoc($res);
echo "Total in p: " . $row['total'] . "\n";

$res2 = mysqli_query($conn, "SELECT count(*) as total FROM products");
$row2 = mysqli_fetch_assoc($res2);
echo "Total in products: " . $row2['total'] . "\n";
?>
