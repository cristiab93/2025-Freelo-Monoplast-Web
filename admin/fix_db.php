<?php
// fix_db.php
// This script repairs the sub_categories table to ensure 'sub_category_father' 
// contains the category KEYWORD instead of the category ID.

$host = 'localhost';
$user = 'cristianb';
$pass = '511xpWgxUR4icML4';
$db   = 'monoplast';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset('utf8mb4');

echo "<pre>";
echo "Starting DB repair...\n";

// 1. Fetch all categories (ID -> Key map)
$cats = [];
$res = $conn->query("SELECT category_id, category_key_word, category_name FROM categories");
while ($row = $res->fetch_assoc()) {
    $cats[$row['category_id']] = $row['category_key_word'];
}
$res->close();

echo "Found " . count($cats) . " categories.\n";

// 2. Update sub_categories
// We iterate through our known categories and update any subcategory that points to the ID
$updated = 0;
foreach ($cats as $id => $slug) {
    echo "Processing Category ID $id ($slug)...\n";
    
    // Check if there are subcats with this ID as father
    // We check for string equality generally, assuming father column might be varchar now
    $stmt = $conn->prepare("UPDATE sub_categories SET sub_category_father = ? WHERE sub_category_father = ?");
    // bind params: first ? is string (slug), second ? is string (id as string)
    $idStr = (string)$id;
    $stmt->bind_param("ss", $slug, $idStr);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo "  -> Updated " . $stmt->affected_rows . " subcategories to use key '$slug'.\n";
        $updated += $stmt->affected_rows;
    }
    $stmt->close();
}

if ($updated === 0) {
    echo "No subcategories needed updating. (They might already use keywords).\n";
} else {
    echo "Total updates: $updated.\n";
}

echo "Done.\n";
echo "</pre>";
?>
