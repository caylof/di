<?php
namespace caylof\test;

class Rabbit implements IRabbit {

    use TBase;

    private $speed;

    public function __construct() {
        $this->speed = [3, 3, 3, 3, 3];
    }

    public function run() {
        return array_sum($this->speed);
    }
}
