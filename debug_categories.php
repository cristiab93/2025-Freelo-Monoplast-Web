<?php
include "_general.php";

$sel = SelectQuery("products");
$rows = $sel->Run();

$counts = [];
foreach ($rows as $r) {
    $cat = $r['product_category'];
    if (!isset($counts[$cat])) $counts[$cat] = 0;
    $counts[$cat]++;
}

echo "Database Category Counts:\n";
foreach ($counts as $cat => $count) {
    echo "- '$cat': $count\n";
}

echo "\nCATEGORY_MAP:\n";
print_r($CATEGORY_MAP);

echo "\nREVERSE MAP (as built in products-all.php):\n";
function norm_txt_debug($s) {
  $s = (string)$s;
  $s = mb_strtolower($s, 'UTF-8');
  $s = iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$s);
  $s = preg_replace('/[^a-z0-9\- ]/',' ', $s);
  $s = preg_replace('/\s+/',' ', trim($s));
  return $s;
}

$rev = [];
foreach ($CATEGORY_MAP as $k => $v) {
  $rev[norm_txt_debug($v)] = $k;
  $rev[norm_txt_debug($k)] = $k;
}
print_r($rev);

echo "\nTesting 'Artefactos y mamparas':\n";
$test = "Artefactos y mamparas";
$norm = norm_txt_debug($test);
echo "Normalized: '$norm'\n";
if (isset($rev[$norm])) {
    echo "Found key: " . $rev[$norm] . "\n";
} else {
    echo "NOT FOUND IN REV MAP\n";
}
?>
