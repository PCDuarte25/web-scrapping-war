<?php

namespace Galoa\ExerciciosPhp2022\WebScrapping;

/**
 * Does the scrapping of a webpage.
 */
class Scrapper {

  /**
   * Loads paper information from the HTML and creates a XLSX file.
   */
  public function scrap(\DOMDocument $dom): void {
    // print $dom->saveHTML();
    $xpath = new \DOMXpath($dom);
    $query = "//a[contains(@class,'paper-card')]";
    $posts = $xpath->query($query);

    $post = $posts->item(0);
    
    $title = getTitle($post, $xpath);
    $type = getTipo($post, $xpath);
    $id = getId($post, $xpath);
    $authors = getAuthors($post, $xpath);
    $institutions = getInstitutions($post, $xpath);
  }
  
}

function getTitle($post, $xpath) {
  $title = $xpath->query("*[contains(@class,'paper-title')]", $post)->item(0)->textContent;
  $formatedTitle = preg_replace('/\s+/i', ' ', $title);
  return $formatedTitle;
}

function getAuthors($post, $xpath) {
  $authors = $xpath->query("*[contains(@class,'authors')]/*", $post);
  $authorsNames = [];
  for ($i = 0; $i < $authors->count(); $i++) {
    $authorName = $authors->item($i)->textContent;
    $formatedAuthorName = preg_replace('/\s+/i', ' ', $authorName);
    $formatedAuthorName = str_replace(';', '', $formatedAuthorName);
    array_push($authorsNames, $formatedAuthorName);
  }

  return $authorsNames;
}

function getInstitutions($post, $xpath) {
  $institutions = $xpath->query("*[contains(@class,'authors')]/*", $post);
  $institutionsNames = [];
  for ($i = 0; $i < $institutions->count(); $i++) {
    $institutionName = $institutions->item($i)->getAttribute('title');
    $formatedInstitutionName = preg_replace('/\s+/i', ' ', $institutionName);
    array_push($institutionsNames, $formatedInstitutionName);
  }

  return $institutionsNames;
}

function getTipo($post, $xpath) {
  $type = $xpath->query("descendant::*[contains(@class,'tags')]", $post)->item(0)->textContent;
  return $type;
}

function getId($post, $xpath) {
  $id = $xpath->query("descendant::*[contains(@class,'volume-info')]", $post)->item(0)->textContent;
  return $id;
}
