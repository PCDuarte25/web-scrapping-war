<?php
namespace Galoa\ExerciciosPhp2022\War\GamePlay\Country;

$country = new ComputerPlayerCountry('Juno');
    $neighbors = [
      new ComputerPlayerCountry('Juno'), 
      new ComputerPlayerCountry('Payon'), 
      new ComputerPlayerCountry('Geffen')
    ];

    $country->setNeighbors($neighbors);

    $vizinhoAtacado = $country->chooseToAttack();

    var_dump($vizinhoAtacado);