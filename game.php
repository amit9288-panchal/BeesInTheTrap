<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/Commands/BeeGame.php';

use Symfony\Component\Console\Application;
use App\Commands\BeeGame;

$app = new Application();
$app->add(new BeeGame());
try {
    $app->run();
} catch (Exception $e) {
    var_dump($e->getMessage());
    die;
}

