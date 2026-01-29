<?php
include "_general.php";

$oldKey = 'artefactos-y-mamparas';
$newKey = 'artefactos';

// Check count before
$sqlCount = "SELECT COUNT(*) as cnt FROM products WHERE product_category = '$oldKey'";
$resCount = mysqli_query($conn, $sqlCount);
$row = mysqli_fetch_assoc($resCount);
echo "Products with '$oldKey' before update: " . $row['cnt'] . "<br>";

if ($row['cnt'] > 0) {
    $sqlUpdate = "UPDATE products SET product_category = '$newKey' WHERE product_category = '$oldKey'";
    if (mysqli_query($conn, $sqlUpdate)) {
        echo "Successfully updated " . mysqli_affected_rows($conn) . " products to '$newKey'.<br>";
    } else {
        echo "Error updating records: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "No products found to update.<br>";
}

// Verify
$sqlCheck = "SELECT COUNT(*) as cnt FROM products WHERE product_category = '$newKey'";
$resCheck = mysqli_query($conn, $sqlCheck);
$rowCheck = mysqli_fetch_assoc($resCheck);
echo "Products with '$newKey' after update: " . $rowCheck['cnt'] . "<br>";
?>
