<?php

require __DIR__.'/vendor/Psr4AutoloadClass.php';
$loader = new vendor\Psr4AutoloaderClass();
$loader->register();
$loader->addNamespace('caylof', __DIR__.'/src');


function debug($var, $isExit = true) {
    print '<pre>';
    var_dump($var);
    print '</pre>';
    if ($isExit) {
        exit();
    }
}

use caylof\Di;
use caylof\test\Rabbit;
use caylof\test\LazyRabbit;
use caylof\test\Tortoise;
use caylof\test\Game;

$di = new Di;
//$di->set('caylof\test\IRabbit', LazyRabbit::className());
$di->set('caylof\test\IRabbit', Rabbit::className());
/*
$di->set('game', function() {
    return [
        'class' => Game::className(),
        'args' => [
            'title' => 'new game'
        ]
    ];
});
$game = $di->get('game');
*/
$game = $di->get(Game::className());
$ret = $game->getResult();
debug($ret);
