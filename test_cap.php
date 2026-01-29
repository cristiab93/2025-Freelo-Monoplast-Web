<?php
include "conn/functions.php";

$test = "¡que Nada Arruine Tu Momento!";
echo "Original: " . $test . "\n";
$cleaned = clean_text($test);
$formatted = truncate_text(title_case($cleaned), 80);
echo "Result: " . $formatted . "\n";

$test2 = "¿sabías que con un Termotanque...";
echo "Original: " . $test2 . "\n";
echo "Result: " . truncate_text(title_case(clean_text($test2)), 80) . "\n";
