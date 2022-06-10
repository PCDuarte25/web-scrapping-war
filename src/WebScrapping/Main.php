<?php

namespace Galoa\ExerciciosPhp2022\WebScrapping;

use DOMDocument;

/**
 * Runner for the Webscrapping exercice.
 */
class Main {

  /**
   * Main runner, instantiates a Scrapper and runs.
   */
  public static function run(): void {
    $dom = new DOMDocument('1.0', 'utf-8');
    @$dom->loadHTMLFile(__DIR__ . '/../../webscrapping/origin.html'); // coloquei o @ temporariamente para poder trabalhar melhor com o documento sem warnings
    (new Scrapper())->scrap($dom);
  }

}
