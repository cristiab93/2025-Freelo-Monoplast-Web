<?php
// fix_db_v2.php
// Iterates row-by-row to fix sub_category_father keys

$host = 'localhost';
$user = 'cristianb';
$pass = '511xpWgxUR4icML4';
$db   = 'monoplast';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset('utf8mb4');

echo "<pre>";
echo "Starting DB repair (Row-by-Row)...\n";

// 1. Fetch Categories MAP (ID => Keyword)
$catMap = [];
$res = $conn->query("SELECT category_id, category_key_word FROM categories");
while ($r = $res->fetch_assoc()) {
    $catMap[$r['category_id']] = $r['category_key_word'];
}
$res->close();

echo "Loaded mappings:\n";
print_r($catMap);

// 2. Fetch All Subcategories
$res = $conn->query("SELECT sub_category_id, sub_category_father, sub_category_name FROM sub_categories");
$updates = 0;

while ($sub = $res->fetch_assoc()) {
    $id = $sub['sub_category_id'];
    $father = $sub['sub_category_father'];
    $name = $sub['sub_category_name'];

    // Check if father is numeric
    if (is_numeric($father)) {
        $fatherId = (int)$father;
        if (isset($catMap[$fatherId])) {
            $newKey = $catMap[$fatherId];
            echo "Subcat '$name' (ID: $id) has father '$father' (ID). Changing to '$newKey'...\n";
            
            // Execute Update
            $uStmt = $conn->prepare("UPDATE sub_categories SET sub_category_father = ? WHERE sub_category_id = ?");
            $uStmt->bind_param("si", $newKey, $id);
            $uStmt->execute();
            $uStmt->close();
            $updates++;
        } else {
            echo "WARNING: Subcat '$name' (ID: $id) has father ID '$father' but no matching Category ID found.\n";
        }
    } else {
        echo "Subcat '$name' (ID: $id) father '$father' seems OK (not numeric).\n";
    }
}
$res->close();

echo "\nTotal updates performed: $updates\n";
echo "Done.\n";
echo "</pre>";
?>
