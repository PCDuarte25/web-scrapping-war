<?php

namespace Galoa\ExerciciosPhp2022\War\GamePlay\Country;

/**
 * Defines a country, that is also a player.
 */
class BaseCountry implements CountryInterface {

  /**
   * The name of the country.
   *
   * @var string
   */
  protected $name;
  protected $neighbors; // Add an array of this country neighbors
  protected $numberOfTroops; // And a number of troops of this country
  protected $conqueredCountries; // Add the number of conquered countries of this country

  /**
   * Builder.
   *
   * @param string $name
   *   The name of the country.
   */
  public function __construct(string $name) {
    $this->name = $name;
    $this->numberOfTroops = 3; // Number of troops at the beginning of the game
    $this->conqueredCountries = 0; // Number of conquered countries at the beggning of the game
  }

  public function getName(): string {
    return $this->name;
  }

  public function setNeighbors(array $neighbors): void {
    $this->neighbors = $neighbors;
  }

  public function getNeighbors(): array {
    return $this->neighbors;
  }

  public function getNumberOfTroops(): int {
    return $this->numberOfTroops;
  }

  public function isConquered(): bool {
    return $this->numberOfTroops <= 0;
  }

  public function conquer(CountryInterface $conqueredCountry): void {
    $this->conqueredCountries += $conqueredCountry->conqueredCountries + 1; // Add + 1 to the counter of conquered countries for the contry attached, and +x for the number of conquered countries of him

    foreach ($conqueredCountry->getNeighbors() as $defender) {

      $neighborAlreadyExist = false;

      for ($i = 0; $i < count($this->neighbors); $i++) {
      $actualNeighbor = $this->neighbors[$i]; //array of neighbours that will receive the attachment
      $attackingCountryName = $this->getName(); // name of the country that will receive the attachment
        if ($actualNeighbor === $defender || $attackingCountryName === $defender->getName()) {
          $neighborAlreadyExist = true;
          break;
        }
      }

      $attachedCountryNeighbors = $defender->getNeighbors();

      if (!$neighborAlreadyExist) {
        array_push($this->neighbors, $defender);
        array_push($attachedCountryNeighbors, $this);
        $defender->neighbors = $attachedCountryNeighbors;
      }
    }
  }

  public function killTroops(int $killedTroops): void {
    $this->numberOfTroops -= $killedTroops;
  }

  // New functions
  public function addTroops(): void {
    $this->numberOfTroops += $this->conqueredCountries + 3;
  }

}
