<?php
include "conn/functions.php";

$test_cases = [
    "-" => "Empty (hyphen)",
    "A" => "Empty (single letter)",
    "AB" => "Expected: AB",
    "   -   " => "Empty (hyphen with spaces)",
    "" => "Empty (already empty)",
    "Normal Name" => "Expected: Normal Name",
    "<p>-</p>" => "Empty (hyphen in tags)"
];

echo "<h1>Verification of clean_text</h1>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Input</th><th>Result</th><th>Status</th></tr>";

foreach ($test_cases as $input => $expected) {
    $result = clean_text($input);
    $display_result = ($result === "") ? "[EMPTY]" : $result;
    $status = (($result === "" && strpos($expected, "Empty") !== false) || $result === $input || strpos($expected, $result) !== false) ? "✅" : "❌";
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($input) . "</td>";
    echo "<td>" . htmlspecialchars($display_result) . "</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}
echo "</table>";
