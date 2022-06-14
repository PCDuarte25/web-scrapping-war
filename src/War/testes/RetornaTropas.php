<?php

function getNumberOfTroops() {
    $paisesConquistados = [['Rohan', 'Juno', 'Payon'], ['Geffen', 'Prontera', 'Isilis']];
    $continentesConquistados = count($paisesConquistados);
    return 3 + count($paisesConquistados, 1) - $continentesConquistados;
}
echo getNumberOfTroops();