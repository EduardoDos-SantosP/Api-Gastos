<?php

include_once 'vendor/autoload.php';

use Edsp\ApiGastos\Application;

function debug($value, bool $die = true): void{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    if ($die) die();
}

Application::run();