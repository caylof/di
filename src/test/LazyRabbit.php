<?php
namespace caylof\test;

class LazyRabbit implements IRabbit {

    use TBase;

    private $speed;

    public function __construct() {
        $this->speed = [3, 0, 0, 0, 3];
    }

    public function run() {
        return array_sum($this->speed);
    }
}
