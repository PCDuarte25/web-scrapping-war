<?php
namespace Galoa\ExerciciosPhp2022\WebScrapping;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
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

    $formatedPosts = [];

    for ($i = 0; $i < $posts->count(); $i++) {
      $post = $posts->item($i);
      
      $title = getTitle($post, $xpath);
      $id = getId($post, $xpath);
      $type = getTipo($post, $xpath);
      $authors = getAuthors($post, $xpath);
      $institutions = getInstitutions($post, $xpath);
      
      array_push($formatedPosts, new Post($title, $id, $type, $authors, $institutions));

      if (!$formatedPosts[$i]->checkPost()) {
        echo "HTML may has to be updated, check for invalid inputs";
        return;
      }
    }
    
    // writer
    $borderHeader = (new BorderBuilder())
    ->setBorderBottom(Color::BLACK, Border::WIDTH_MEDIUM, Border::STYLE_SOLID)
    ->setBorderTop(Color::BLACK, Border::WIDTH_MEDIUM, Border::STYLE_SOLID)
    ->setBorderLeft(Color::BLACK, Border::WIDTH_MEDIUM, Border::STYLE_SOLID)
    ->setBorderRight(Color::BLACK, Border::WIDTH_MEDIUM, Border::STYLE_SOLID)
    ->build();

    $styleHeader = (new StyleBuilder())
    ->setFontBold()
    ->setFontSize(12)
    ->setFontColor(Color::WHITE)
    ->setFontName('Arial')
    ->setCellAlignment(CellAlignment::CENTER)
    ->setBackgroundColor(Color::rgb(77,30,87))
    ->setBorder($borderHeader)
    ->build();

    $borderRows = (new BorderBuilder())
    ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
    ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
    ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
    ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
    ->build();

    $styleHRowsLP = (new StyleBuilder())
    ->setFontSize(12)
    ->setFontColor(Color::BLACK)
    ->setFontName('Arial')
    ->setCellAlignment(CellAlignment::CENTER)
    ->setBackgroundColor(Color::rgb(215,215,234))
    ->setBorder($borderRows)
    ->build();

    $styleHRowsP = (new StyleBuilder())
    ->setFontSize(12)
    ->setFontColor(Color::BLACK)
    ->setFontName('Arial')
    ->setCellAlignment(CellAlignment::CENTER)
    ->setBackgroundColor(Color::rgb(217,222,249))
    ->setBorder($borderRows)
    ->build();

    $writer = WriterEntityFactory::createXLSXWriter();
    
    $writer->openToFile('Posts.xlsx'); 

    $values = ['ID', 'Title', 'Type'];

    $biggerArray = getBiggerArray($formatedPosts);
    createHeader($biggerArray, $values);

    $rowFromValues = WriterEntityFactory::createRowFromArray($values, $styleHeader);
    $writer->addRow($rowFromValues);


    for ($i = 0; $i < count($formatedPosts); $i++) {
      $values2 =  [$formatedPosts[$i]->getId(), $formatedPosts[$i]->getTitle(), $formatedPosts[$i]->getType()];

      addAuthorAndInstitution($formatedPosts[$i], $values2);

      $backGroundColor = $i % 2 === 0 ? $styleHRowsLP :  $styleHRowsP;

      $rowFromValues2 = WriterEntityFactory::createRowFromArray($values2, $backGroundColor);
      $writer->addRow($rowFromValues2);
    }
    
    $writer->close();
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
    if (!$formatedAuthorName || $formatedAuthorName === ' ') {
      continue;
    }
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
    if (!$formatedInstitutionName || $formatedInstitutionName === ' ') {
      continue;
    }
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

function addAuthorAndInstitution($post, &$values) {
  $authors = $post->getAuthors();
  $institutions = $post->getInstitutions();
  for ($i = 0; $i < count($authors); $i++) {
    $author = $authors[$i];
    $institution = $institutions[$i];

    array_push($values, $author);
    array_push($values, $institution);
  }
}

function getBiggerArray($posts) {
  $biggerArray = 0;
  for ($i = 0; $i < count($posts); $i++) {
    if(count($posts[$i]->getAuthors()) > $biggerArray) {
      $biggerArray = count($posts[$i]->getAuthors());
    }
  }
  return $biggerArray;
}

function createHeader($size, &$values) {
  for ($i = 1; $i <= $size; $i++) {
    array_push($values, "Author{$i}");
    array_push($values, "Institution{$i}");
  }
}
