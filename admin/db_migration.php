<?php
include_once("../conn/cfg.php");

$host = DBSERVERNAME;
$user = DBUSERNAME;
$pass = DBPASSWORD;
$db   = DBNAME;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset('utf8mb4');

function make_slug($text) {
    // Basic slugify
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    return $text;
}

// 1. Ensure columns exist and have correct types
// We want sub_category_father to be VARCHAR(100) to hold the parent's keyword
// We also want category_key_word and sub_category_key_word

// Categories
try {
    $conn->query("ALTER TABLE categories ADD COLUMN category_key_word VARCHAR(100) AFTER category_name");
} catch (Exception $e) {}

// Subcategories
// If sub_category_father is already int, we need to change it to varchar.
// WARNING: This invalidates existing int relations if we don't migrate data, but we are truncating anyway.
try {
    $conn->query("ALTER TABLE sub_categories MODIFY COLUMN sub_category_father VARCHAR(100)");
} catch (Exception $e) {
    echo "Error modifying sub_category_father: " . $e->getMessage() . "\n";
}

try {
    $conn->query("ALTER TABLE sub_categories ADD COLUMN sub_category_key_word VARCHAR(100) AFTER sub_category_name");
} catch (Exception $e) {}

// Products
try {
    $conn->query("ALTER TABLE products MODIFY COLUMN product_category VARCHAR(100)");
    $conn->query("ALTER TABLE products MODIFY COLUMN product_subcategory VARCHAR(100)");
} catch(Exception $e) {}


// 2. Truncate tables for fresh start with new structure
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE categories");
$conn->query("TRUNCATE TABLE sub_categories");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");


// 3. Insert Data
$data = [
    'ARTEFACTOS Y MAMPARAS' => ['Artefactos', 'Grifería', 'Mamparas'],
    'CALEFACCIÓN' => ['Calderas', 'Losa radiante', 'Radiadores', 'Termotanques'],
    'CONSTRUCCIÓN' => ['Agua', 'Gas', 'Cloacal', 'PVC', 'Polipropileno', 'Galvanizado / Epoxi', 'Bombas', 'Canaletas', 'Tanques', 'Válvulas'],
    'INFRAESTRUCTURA' => ['Fibra óptica', 'Caños', 'Caño perfilado', 'PEAD'],
    'PILETAS' => ['Electrobombas', 'Filtros', 'Limpieza y mantenimiento', 'Productos para mantenimiento', 'Mangueras'],
    'RIEGO' => ['Aspersores', 'Mangueras', 'Caños hidráulicos']
];

foreach ($data as $catName => $subs) {
    $catSlug = make_slug($catName);
    
    // Insert Category
    $stmt = $conn->prepare("INSERT INTO categories (category_name, category_key_word) VALUES (?, ?)");
    $stmt->bind_param("ss", $catName, $catSlug);
    $stmt->execute();
    $stmt->close();
    
    // Insert Subcategories linking by father's SLUG
    foreach ($subs as $subName) {
        $subSlug = make_slug($subName);
        
        $stmtSub = $conn->prepare("INSERT INTO sub_categories (sub_category_father, sub_category_name, sub_category_key_word) VALUES (?, ?, ?)");
        $stmtSub->bind_param("sss", $catSlug, $subName, $subSlug); // sub_category_father is now string (catSlug)
        $stmtSub->execute();
        $stmtSub->close();
    }
}

echo "Database updated successfully (Relationships now use keywords).\n";
?>
