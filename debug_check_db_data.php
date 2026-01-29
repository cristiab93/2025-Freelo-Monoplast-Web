<?php
require_once "conn/cfg.php";
require_once "conn/sql_latest.php";

echo "--- Categories Table ---\n";
$cats = SelectQuery("categories")->SetIndex(-1)->Run();
if ($cats) {
    foreach ($cats as $c) {
        echo "Key: " . ($c['category_key_word'] ?? 'NULL') . ", Name: " . ($c['category_name'] ?? 'NULL') . "\n";
    }
} else {
    echo "Categories table is empty or query failed.\n";
}

echo "\n--- Distinct Product Categories ---\n";
// Since SelectQuery might not support DISTINCT easily without custom SQL, I'll fetch all and unique them in PHP, or just check a subset.
// Actually, I can use SelectQuery with raw query if needed, but let's just fetch all products and aggregate counts.
$products = SelectQuery("products")->SetIndex(-1)->Run();
$counts = [];
foreach ($products as $p) {
    $cat = $p['product_category'] ?? '(empty)';
    if (!isset($counts[$cat])) $counts[$cat] = 0;
    $counts[$cat]++;
}

foreach ($counts as $cat => $count) {
    echo "Category: '$cat' => Count: $count\n";
}

echo "\n--- Subcategories Table ---\n";
$subs = SelectQuery("sub_categories")->SetIndex(-1)->Run();
if ($subs) {
    foreach ($subs as $s) {
        echo "Key: " . ($s['sub_category_key_word'] ?? 'NULL') . ", Name: " . ($s['sub_category_name'] ?? 'NULL') . "\n";
    }
}
