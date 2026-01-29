<?php
// debug_delete_test.php
// Creates a dummy product and attempts to delete it to verify the backend logic.

$_SESSION['admin_user'] = 'debug_user'; // Mock session for _general.php checks if needed, though CLI might bypass

include('../_general.php'); // Adjust path as we are in admin/

echo "<pre>\n";
echo "--- DEBUG DELETE TEST ---\n";

// 1. Create Dummy Product
$testName = "DEBUG_DELETE_" . time();
echo "Creating product '$testName'...\n";

InsertQuery('products')
    ->Value('product_name','s',$testName)
    ->Value('product_subname','s','Test Sub')
    ->Value('product_category','s','test_cat')
    ->Value('product_subcategory','s','test_sub')
    ->Value('product_img','s','')
    ->Value('product_description','s','Test Desc')
    ->Value('product_size','s','10x10')
    ->Value('product_date','s',date('Y-m-d'))
    ->Run(1); // Enable SQL printing

// 2. Find its ID
$res = SelectQuery('products')
    ->Condition("product_name = ", "s", $testName)
    ->Run();

if (!$res || empty($res)) {
    die("ERROR: Failed to create test product.\n");
}

$product = $res[0];
$id = (int)$product['product_id'];

echo "Created Product ID: $id\n";

// 3. Attempt Delete
echo "Attempting DeleteQuery for ID $id...\n";

DeleteQuery('products')
    ->Condition('product_id =','i',$id)
    ->Run(1); // Enable printing query if supported

// 4. Verify Deletion
$check = SelectQuery('products')
    ->Condition("product_id =", "i", $id)
    ->Run();

if (empty($check)) {
    echo "SUCCESS: Product ID $id was deleted.\n";
} else {
    echo "FAILURE: Product ID $id still exists!\n";
    print_r($check);
}

echo "</pre>\n";
?>
