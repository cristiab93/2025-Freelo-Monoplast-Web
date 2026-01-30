<?php
// Standalone CLI debug script
$conn = mysqli_connect('localhost', 'cristianb', '511xpWgxUR4icML4', 'monoplast');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$cat = 'construccion';
$query = "SELECT product_id, product_name, product_img FROM products WHERE product_category = '$cat'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

echo "ID|Name|ImageRaw|Exists" . PHP_EOL;

while ($row = mysqli_fetch_assoc($result)) {
    $raw = trim($row['product_img']);
    $isAbs = preg_match('/^https?:\/\//i', $raw) || (strlen($raw) && $raw[0] === '/');
    
    $exists = "N/A";
    if ($raw === "") {
        $exists = "EMPTY";
    } else if (!$isAbs) {
        $filePath = "c:\\MAMP\\htdocs\\freelo_monoplast\\uploaded_img\\" . ltrim($raw, '/');
        $exists = file_exists($filePath) ? "YES" : "NO";
    } else {
        $exists = "ABS";
    }
    
    echo $row['product_id'] . "|" . $row['product_name'] . "|" . $raw . "|" . $exists . PHP_EOL;
}
mysqli_close($conn);
