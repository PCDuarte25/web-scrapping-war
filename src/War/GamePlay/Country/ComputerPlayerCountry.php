<?php

namespace Galoa\ExerciciosPhp2022\War\GamePlay\Country;

/**
 * Defines a country that is managed by the Computer.
 */
class ComputerPlayerCountry extends BaseCountry {

  /**
   * Choose one country to attack, or none.
   *
   * The computer may choose to attack or not. If it chooses not to attack,
   * return NULL. If it chooses to attack, return a neighbor to attack.
   *
   * It must NOT be a conquered country.
   *
   * @return \Galoa\ExerciciosPhp2022\War\GamePlay\Country\CountryInterface|null
   *   The country that will be attacked, NULL if none will be.
   */
  public function chooseToAttack(): ?CountryInterface {
    $isAttacking =  rand(0, 1);
    if ($isAttacking === 0 || $this->getNumberOfTroops() <= 1) return NULL; 

    $neighbors = $this->getNeighbors();

    foreach($neighbors as $neighbor) {
      if (!$neighbor->isConquered()) {
        // echo "possibilidades de ataque: {$neighbor->getName()}\n"; Debugger para checar as possibilidades de ataque
      }
    }

    do {
      $sortedNumber = rand(0, count($neighbors) - 1);
    } while($neighbors[$sortedNumber]->isConquered());
    return $neighbors[$sortedNumber];
  }
}