<?php
// Mocking mb_ functions if they are missing (though they shouldn't be)
if (!function_exists('mb_strtolower')) {
    function mb_strtolower($str, $enc = 'UTF-8') { return strtolower($str); }
}
if (!function_exists('mb_strtoupper')) {
    function mb_strtoupper($str, $enc = 'UTF-8') { return strtoupper($str); }
}
if (!function_exists('mb_substr')) {
    function mb_substr($str, $start, $len = null, $enc = 'UTF-8') { return substr($str, $start, $len); }
}

include "conn/functions.php";

$test_names = [
    "FV 0108/B1 ARIZONA - JUEGO MONOCOMANDO PARA BIDÉ",
    "PIAZZA DE PORCELANA CON GRIFERIA",
    "0406/B1 ARIZONA - JUEGO MONOCOMANDO PARA PARED DE COCINA",
    "PRODUCTO CON Y SIN ALGO",
    "LO MEJOR DE LO MEJOR"
];

foreach ($test_names as $name) {
    echo "Original: " . $name . "\n";
    echo "Formatted: " . title_case($name) . "\n";
    echo "-------------------\n";
}
