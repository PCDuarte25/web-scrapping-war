<?php

namespace Galoa\ExerciciosPhp2022\War\GamePlay;

use Galoa\ExerciciosPhp2022\War\GamePlay\Country\CountryInterface;

/**
 * A manager that will roll the dice and compute the winners of a battle.
 */

class Battlefield implements BattlefieldInterface {

    public function rollDice(CountryInterface $country, bool $isAttacking): array {
        if ($isAttacking) {
            $attackingTroops = $country->getNumberOfTroops() - 1; // Quantity of dices that attacker will roll
            $attackingDice = []; // Array of dice from the attacker

            for($i = 0; $i < $attackingTroops; $i++) {
                $actualDie = rand(1, 6);
                array_push($attackingDice, $actualDie);
            }
            rsort($attackingDice);
            return $attackingDice;
        } else {
            $defendingTroops = $country->getNumberOfTroops(); // Quantity of dices that defender will roll
            $defendingDice = []; // Array of dice from the defender

            for($i = 0; $i < $defendingTroops; $i++) {
                $actualDie = rand(1, 6);
                array_push($defendingDice, $actualDie);
            }
            rsort($defendingDice);
            return $defendingDice;
        }
    }
    
    public function computeBattle(CountryInterface $attackingCountry, array $attackingDice, CountryInterface $defendingCountry, array $defendingDice): void { 
        $countryWithLessTroops = min(count($attackingDice), count($defendingDice));

        $killedTroopsDefense = 0;
        $killedTroopsAttack = 0;

        for ($i = 0; $i < $countryWithLessTroops; $i++) {
            if ($attackingDice[$i] >= $defendingDice[$i]) {
                $killedTroopsDefense += 1;
            } else {
                $killedTroopsAttack += 1;
            }
        }

        $attackingCountry->killTroops($killedTroopsAttack);
        $defendingCountry->killTroops($killedTroopsDefense);
    }
}
