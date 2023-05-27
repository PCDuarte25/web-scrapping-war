<?php

namespace Galoa\ExerciciosPhp2022\WebScrapping;

use DOMDocument;

/**
 * Mudei so pra ter um pr
 */
class Main {

  public static function run(): void {
    $dom = new DOMDocument('1.0', 'utf-8');
    @$dom->loadHTMLFile(__DIR__ . '/../../webscrapping/origin.html'); // coloqemporariamente para poder trabalhar melhor com o documento sem warnings
    (new Scrapper())->scrap($dom);
  }

}
