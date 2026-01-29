<?php
include "_general.php";

function norm_txt($s) {
  $s = (string)$s;
  $s = mb_strtolower($s, 'UTF-8');
  $s = iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$s);
  $s = preg_replace('/[^a-z0-9\- ]/',' ', $s);
  $s = preg_replace('/\s+/',' ', trim($s));
  return $s;
}

$sql = "SELECT product_category, COUNT(*) as cnt FROM products GROUP BY product_category";
$result = mysqli_query($conn, $sql);

echo "<h1>Categories in DB (with counts):</h1>";
echo "<ul>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<li>[" . htmlspecialchars($row['product_category']) . "] - count: " . $row['cnt'] . "</li>";
}
echo "</ul>";

echo "<h1>Category Map in PHP:</h1>";
echo "<pre>";
print_r($CATEGORY_MAP);
echo "</pre>";

echo "<h1>Normalization Test:</h1>";
foreach ($CATEGORY_MAP as $key => $val) {
    echo "Key: [$key] -> norm: [" . norm_txt($key) . "]<br>";
    echo "Val: [$val] -> norm: [" . norm_txt($val) . "]<br>";
}

echo "<h1>Test Specific String:</h1>";
$test_str = "Artefactos y mamparas";
echo "Original: [$test_str] -> Norm: [" . norm_txt($test_str) . "]<br>";
?>
