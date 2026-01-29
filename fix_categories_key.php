<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Hardcoded creds (robust for CLI)
$host = "localhost";
$user = "cristianb";
$pass = "511xpWgxUR4icML4";
$db = "monoplast";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Connect failed: " . mysqli_connect_error());
mysqli_set_charset($conn, 'utf8mb4');

$oldKey = 'artefactos-y-mamparas';
$newKey = 'artefactos';

echo "Migrating from '$oldKey' to '$newKey'...\n";

// 1. Categories
$sqlCat = "UPDATE categories SET category_key_word = '$newKey' WHERE category_key_word = '$oldKey'";
if (mysqli_query($conn, $sqlCat)) {
    echo "Categories updated: " . mysqli_affected_rows($conn) . "\n";
} else {
    echo "Error updating categories: " . mysqli_error($conn) . "\n";
}

// 2. Subcategories
$sqlSub = "UPDATE sub_categories SET sub_category_father = '$newKey' WHERE sub_category_father = '$oldKey'";
if (mysqli_query($conn, $sqlSub)) {
    echo "Subcategories updated: " . mysqli_affected_rows($conn) . "\n";
} else {
    echo "Error updating subcategories: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>
