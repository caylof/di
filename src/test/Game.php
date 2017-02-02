<?php
namespace caylof\test;

class Game {

    use TBase;

    private $rabbit;
    private $tortoise;

    public function __construct(IRabbit $rabbit, Tortoise $tortoise, $title = 'the game of rabbit and tortoise') {
        $this->rabbit = $rabbit;
        $this->tortoise = $tortoise;
        $this->title = $title;
    }

    public function getResult() {
        return $this->rabbit->run() > $this->tortoise->crawl() ? 'rabbit win' : 'tortoise win';
    }
}
