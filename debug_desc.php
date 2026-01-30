<?php
require_once "_general.php";

$products = SelectQuery("products")->Limit(5)->Run();
foreach ($products as $p) {
    echo "ID: " . $p['product_id'] . "\n";
    echo "Name: " . $p['product_name'] . "\n";
    echo "Description Raw: \n";
    var_dump($p['product_description']);
    echo "Description Cleaned: \n";
    var_dump(clean_text($p['product_description']));
    echo "-----------------------------------\n";
}
