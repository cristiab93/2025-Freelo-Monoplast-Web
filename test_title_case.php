<?php
require_once("_general.php");

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
