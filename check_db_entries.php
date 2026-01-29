<?php
include_once("_general.php");

$res = SelectQuery("presupuestos")->Order("id", "DESC")->Limit(5)->Run();

if ($res) {
    echo "<table border='1'><tr><th>ID</th><th>Hash</th><th>Client</th><th>Prod ID</th><th>Prod Name</th><th>Qty</th></tr>";
    foreach ($res as $r) {
        echo "<tr>";
        echo "<td>" . $r['id'] . "</td>";
        echo "<td>" . substr($r['budget_hash'], 0, 8) . "...</td>";
        echo "<td>" . $r['client_name'] . "</td>";
        echo "<td>" . $r['product_id'] . "</td>";
        echo "<td>" . $r['product_name'] . "</td>";
        echo "<td>" . $r['product_qty'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No results found in 'presupuestos' table.";
}
?>
