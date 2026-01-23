<?php
require_once("_general.php");

function show_schema($table) {
    global $conn;
    echo "<h3>Schema for table: $table</h3>";
    $res = mysqli_query($conn, "DESCRIBE $table");
    echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
    while ($row = mysqli_fetch_assoc($res)) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td></tr>";
    }
    echo "</table>";
}

show_schema('p');
show_schema('products');
?>
