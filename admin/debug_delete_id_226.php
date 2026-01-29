<?php
// debug_delete_id_226.php
// Attempts to delete product ID 226 specifically

include('../_general.php');

echo "<pre>\n";
echo "--- ATTEMPTING TO DELETE PRODUCT ID 226 ---\n";

// 1. Check if product exists
$res = SelectQuery('products')
    ->Condition("product_id = ", "i", 226)
    ->Run();

if (empty($res)) {
    echo "Product ID 226 does NOT exist in database.\n";
    echo "</pre>";
    exit;
}

echo "Product ID 226 found:\n";
print_r($res[0]);

// 2. Attempt Delete
echo "\nAttempting DeleteQuery for ID 226...\n";

try {
    DeleteQuery('products')
        ->Condition('product_id =','i', 226)
        ->Run(1); // Enable printing query
    
    echo "DeleteQuery executed without PHP errors.\n";
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}

// 3. Verify Deletion
echo "\nChecking if product still exists...\n";
$check = SelectQuery('products')
    ->Condition("product_id =", "i", 226)
    ->Run();

if (empty($check)) {
    echo "SUCCESS: Product ID 226 was deleted.\n";
} else {
    echo "FAILURE: Product ID 226 still exists!\n";
    print_r($check[0]);
}

echo "</pre>\n";
?>
