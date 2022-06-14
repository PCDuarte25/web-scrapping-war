<?php

$izlude = ['Prontera', 'Payon', 'Geffen'];
$brasilis = ['Juno', 'Payon', 'Geffen', 'Morroc', 'Izlude'];
// $atacante = ['Prontera', 'Payon', 'Geffen', 'Juno', 'Morroc'];

$conqueringCountry = $izlude;

//                          Payon
foreach ($brasilis as $defensor) {
    $alreadyExist = false;
    for ($i = 0; $i < count($conqueringCountry); $i++) {
    $atacanteAtual = $conqueringCountry[$i];
//     Prontera // Payon // Geffen // Juno === Payon
        if ($atacanteAtual === $defensor || $defensor === 'Izlude') {
            $alreadyExist = true;
            break;
        }
    }
    if (!$alreadyExist) {
        array_push($conqueringCountry, $defensor);
    }

    $izlude = $conqueringCountry;
}

print_r($izlude);