<?php
namespace caylof\test;

class Tortoise implements ITortoise {

    use TBase;

    private $speed;

    public function __construct() {
        $this->speed = [2, 2, 2, 2, 2];
    }

    public function crawl() {
        return array_sum($this->speed);
    }
}
