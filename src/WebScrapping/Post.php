<?php

namespace Galoa\ExerciciosPhp2022\WebScrapping;

class Post {
    private $title;
    private $id;
    private $type;
    private $authors;
    private $institutions;

    public function __construct($title, $id, $type, $authors, $institutions) {
        $this->title = $title;
        $this->id = $id;
        $this->type = $type;
        $this->authors = $authors;
        $this->institutions = $institutions;
    }

    public function checkPost() {
        if (count($this->authors) !== count($this->institutions)) {
            echo "the arrays haven't the same lenght\n";
            return false;
        }

        if (!$this->title || $this->title === ' ' || is_null($this->title)) {
            echo "The title is invalid: {$this->title} id: {$this->id}\n";
            return false;
        }

        if (!$this->id || $this->id === ' ' || is_null($this->id)) {
            echo "The id is invalid\n";
            return false;
        }

        if (!$this->type || $this->type === ' ' || is_null($this->type)) {
            echo "The type is invalid\n";
            return false;
        }

        for ($i = 0; $i < count($this->authors); $i++) {
            if (!$this->authors[$i] || $this->authors[$i] === ' ' || is_null($this->authors[$i])) {
                echo "The author is invalid\n";
                return false;
            }
        }

        for ($i = 0; $i < count($this->institutions); $i++) {
            if (!$this->institutions[$i] || $this->institutions[$i] === ' ' || is_null($this->institutions[$i])) {
                echo "The institutions is invalid\n";
                return false;
            }
        }

        return true;
    }
}
