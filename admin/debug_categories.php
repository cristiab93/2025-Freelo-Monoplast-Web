<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Mock dependencies
function salir_mant($msg) { die("SALIR_MANT: $msg"); }
define("DBNAME", "monoplast");

// Hardcoded creds
$host = "localhost";
$user = "cristianb";
$pass = "511xpWgxUR4icML4";
$db = "monoplast";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Connect failed: " . mysqli_connect_error());
mysqli_set_charset($conn, 'utf8mb4');

// Make global for SelectQuery
$GLOBALS['conn'] = $conn;

// Include only what is needed
$root = realpath(__DIR__ . "/../conn/database/");
echo "Root path: $root<br>";

$f1 = $root . "/query.php";
$f2 = $root . "/select.php";

if (!file_exists($f1)) die("$f1 not found");
if (!file_exists($f2)) die("$f2 not found");

require $f1;
echo "Included query.php<br>";
require $f2;
echo "Included select.php<br>";

echo "<h1>Debug Categories</h1>";

try {
    $q = new SelectQuery('categories');
    $rows = $q->Run();
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

echo "Count: " . count($rows) . "<Br>";
echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Key Word</th></tr>";
foreach ($rows as $r) {
    echo "<tr>";
    echo "<td>" . ($r['category_id'] ?? '') . "</td>";
    echo "<td>" . ($r['category_name'] ?? '') . "</td>";
    echo "<td>[" . ($r['category_key_word'] ?? '') . "]</td>";
    echo "</tr>";
}
echo "</table>";
?>
