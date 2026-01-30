<?php
include "_general.php";

$rows = SelectQuery('products')->SetIndex(-1)->Run();
$audit = [];
$by_name = [];

foreach ($rows as $r) {
    $raw = trim($r['product_img']);
    $is_ext = preg_match('/^https?:\/\//i', $raw);
    $exists = false;
    
    if ($raw === "") {
        $status = "EMPTY";
    } elseif ($is_ext) {
        $status = "EXTERNAL";
        // Check if domain is the problematic one
        if (strpos($raw, 'alaindecoud.com') !== false) $status = "EXT_BROKEN_ALAIN";
        elseif (strpos($raw, 'monoplast.com.ar') !== false) $status = "EXT_OLD_MONOPLAST";
    } else {
        $path = "uploaded_img/" . ltrim($raw, '/');
        $exists = file_exists($path);
        $status = $exists ? "LOCAL_OK" : "LOCAL_MISSING";
    }

    $entry = [
        'id' => $r['product_id'],
        'name' => trim($r['product_name']),
        'cat' => $r['product_category'],
        'sub' => $r['product_subcategory'],
        'img' => $raw,
        'status' => $status
    ];
    
    $audit[] = $entry;
    $key = strtolower(trim($r['product_name'])) . '|' . strtolower(trim($r['product_category']));
    $by_name[$key][] = $entry;
}

echo "<h1>Full Product Audit</h1>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Status</th><th>Count</th></tr>";
$stats = array_count_values(array_column($audit, 'status'));
foreach ($stats as $s => $c) echo "<tr><td>$s</td><td>$c</td></tr>";
echo "</table>";

echo "<h2>Duplicate Analysis (Same Name & Category)</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Name | Category</th><th>Products (ID: Status)</th><th>Action Suggestion</th></tr>";

foreach ($by_name as $key => $prods) {
    if (count($prods) > 1) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($key) . "</td>";
        echo "<td>";
        foreach ($prods as $p) {
            echo "ID " . $p['id'] . ": " . $p['status'] . "<br>";
        }
        echo "</td>";
        
        // Suggestion logic
        $working = array_filter($prods, function($p) { return $p['status'] === 'LOCAL_OK'; });
        $broken = array_filter($prods, function($p) { return strpos($p['status'], 'EXT_') !== false || $p['status'] === 'LOCAL_MISSING'; });
        
        if (count($working) > 0 && count($broken) > 0) {
            echo "<td>Keep " . implode(',', array_column($working, 'id')) . ", REMOVE " . implode(',', array_column($broken, 'id')) . "</td>";
        } else {
            echo "<td>Needs review</td>";
        }
        echo "</tr>";
    }
}
echo "</table>";
