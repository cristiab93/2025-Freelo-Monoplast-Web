<?php
include "_general.php";

$cat = 'construccion';
$sel = SelectQuery('products')->Condition('product_category =', 's', $cat)->SetIndex(-1)->Run();

echo "<h1>Products in category: $cat</h1>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Image Path</th><th>Absolute?</th><th>File Exists?</th><th>Final URL</th></tr>";

foreach ($sel as $r) {
    $raw = trim($r['product_img']);
    $isAbs = preg_match('/^https?:\/\//i', $raw) || (strlen($raw) && $raw[0] === '/');
    
    $filePath = "";
    if (!$isAbs) {
        $filePath = "uploaded_img/" . ltrim($raw, '/');
        $exists = file_exists($filePath) ? "✅ Yes" : "❌ No";
    } else {
        $exists = "N/A (External)";
    }
    
    // Logic from load-all-products.php
    $finalUrl = $raw !== '' ? ($isAbs ? $raw : ('uploaded_img/' . ltrim($raw, '/'))) : 'img/placeholder.png';

    echo "<tr>";
    echo "<td>" . $r['product_id'] . "</td>";
    echo "<td>" . htmlspecialchars($r['product_name']) . "</td>";
    echo "<td>" . htmlspecialchars($raw) . "</td>";
    echo "<td>" . ($isAbs ? 'Yes' : 'No') . "</td>";
    echo "<td>" . $exists . "</td>";
    echo "<td>" . htmlspecialchars($finalUrl) . "</td>";
    echo "</tr>";
}
echo "</table>";
