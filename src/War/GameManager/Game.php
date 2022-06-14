<?php

namespace Galoa\ExerciciosPhp2022\War\GameManager;

use Galoa\ExerciciosPhp2022\War\GamePlay\Battlefield;
use Galoa\ExerciciosPhp2022\War\GamePlay\BattlefieldInterface;
use Galoa\ExerciciosPhp2022\War\GamePlay\Country\ComputerPlayerCountry;
use Galoa\ExerciciosPhp2022\War\GamePlay\Country\HumanPlayerCountry;

/**
 * Defines a Game, it holds the players and interacts with the UI.
 */
class Game {

  /**
   * The battlefield.
   *
   * @var \Galoa\ExerciciosPhp2022\War\GamePlay\BattlefieldInterface
   */
  protected $battlefield;

  /**
   * The countries in the game, including conquered ones, indexed by name.
   *
   * @var \Galoa\ExerciciosPhp2022\War\GamePlay\Country\CountryInterface[]
   */
  protected $countries;

  /**
   * Instantiates a new game.
   */
  public static function create(): Game {
    return new static(new Battlefield(), CountryList::createWorld());
  }

  /**
   * Builder.
   *
   * @param \Galoa\ExerciciosPhp2022\War\GamePlay\BattlefieldInterface $battlefield
   *   The battle field.
   * @param \Galoa\ExerciciosPhp2022\War\GamePlay\Country\CountryInterface[] $countries
   *   A list of countries.
   */
  public function __construct(BattlefieldInterface $battlefield, array $countries) {
    $this->battlefield = $battlefield;
    $this->countries = $countries;
  }

  /**
   * Plays the game.
   */
  public function play(): void {
    $i = 0;
    while (!$this->gameOver()) {
      $i++;
      print "===== Rodada # $i =====\n";
      // Add troops on the begin of the game for every playing country
      foreach ($this->getUnconqueredCountries() as $playingCountry) {
        if($i > 1) {
          $playingCountry->addTroops();
        }
      }
      $this->stats();
      $this->playRound();
    }
  }

  /**
   * Display stats.
   */
  public function stats(): void {
    foreach ($this->countries as $country) {
      print "  " . $country->getName() . ": " . ($country->isConquered() ? "DERROTADO" : $country->getNumberOfTroops() . " tropas") . "\n";
    }
  }

  /**
   * Displays the game results.
   */
  public function results(): void {
    $countries = $this->getUnconqueredCountries();
    // Should have only one.
    if ($winner = reset($countries)) {
      print "~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~\n";
      print "~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~\n";
      print $winner->getName() . " conquistou toda a Terra-Média!!!\n";
      print "~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~\n";
      print "~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~*~\n";
    }

    $this->stats();
  }

  /**
   * Plays one round.
   */
  protected function playRound(): void {
    foreach ($this->getUnconqueredCountries() as $attackingCountry) { // Percorrer a array de pasises não conquistados e apelidar de attackingCountry
      print "----- Vez de " . $attackingCountry->getName() . "\n"; // Printa a vez do jogador atual
      
      $defendingCountry = NULL;
      if ($attackingCountry instanceof ComputerPlayerCountry) { // Checa se o jogador atual é uma instância de computador
        $defendingCountry = $attackingCountry->chooseToAttack(); // Define o país defensor como quem o ataque escolheu para defender
      }
      elseif ($attackingCountry instanceof HumanPlayerCountry) { // Checa se o jogador atual é uma instância de humano
        $neighbors = $attackingCountry->getNeighbors(); // Pega os paises vizinhos do jogador humano
        $defendingCountryName = NULL;
        do {
          $typedName = readline("Digite o nome de um país para atacar ou deixe em branco para não atacar ninguém:\n"); // Pede para o jogador humano escolher um pais a atacar
          $defendingCountryName = trim($typedName);
        }
        while ($defendingCountryName && !isset($neighbors[$defendingCountryName])); // Checa se o pais escolhido foi algum dos vizinhos existentes

        if ($defendingCountryName) {
          $defendingCountry = $this->countries[$defendingCountryName]; // Define o defensor do pais atacante humano
        }
      }

      // If there is an attack, let's do battle.
      if ($defendingCountry) { // Checa se existe pais a defender, ou seja alguem atacou
        print "  vai atacar " . $defendingCountry->getName() . "\n"; // printa quem está defendendo

        $attackingDice = $this->battlefield->rollDice($attackingCountry, TRUE); // Pega a array de dados do pais atacante
        $defendingDice = $this->battlefield->rollDice($defendingCountry, FALSE); // Pega a array de dados do pais defensor

        print "  dados de " . $attackingCountry->getName() . ": " . implode("-", $attackingDice) . "\n"; // Printa os dados de ataque
        print "  dados de " . $defendingCountry->getName() . ": " . implode("-", $defendingDice) . "\n"; // Printa os dados de defesa

        $this->battlefield->computeBattle($attackingCountry, $attackingDice, $defendingCountry, $defendingDice); // Vai checar quem ganhou a batalha

        if ($defendingCountry->isConquered()) { // Checa se o pais defensor foi conquistado
          $attackingCountry->conquer($defendingCountry); // Se o pais defensor foi conqusitado, ele vai ser anexado pelo atacante
          print "  " . $defendingCountry->getName() . " foi anexado por " . $attackingCountry->getName() . "!\n"; // Printa dizendo que o pais defensor foi anexado
        }
        else { // Se o pais defensor conseguiu se defender, ele não vai ser anexado
          print "  " . $defendingCountry->getName() . " conseguiu se defender!\n"; // Printa que o pais defensor conseguiu se defender
        }
      }
      sleep(1);
    }
  }

  /**
   * Checks is the game is complete.
   *
   * @return bool
   *   TRUE if the game is over, FALSE otherwise.
   */
  protected function gameOver(): bool {
    // If there is only one remaining country, the game is over.
    return count($this->getUnconqueredCountries()) <= 1;
  }

  /**
   * Lists countries that have not been conquered.
   *
   * @return \Galoa\ExerciciosPhp2022\War\GamePlay\Country\CountryInterface[]
   *   An array of countries.
   */
  protected function getUnconqueredCountries(): array {
    return array_filter($this->countries, function($country) {
      return !$country->isConquered();
    });
  }

}
