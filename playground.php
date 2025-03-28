<?php

declare(strict_types=1);

use ExeQue\PlaceholdDotCo\Client;
use ExeQue\PlaceholdDotCo\Placehold;

require 'vendor/autoload.php';

$placehold = new Placehold();

$base = $placehold->builder()->size(1920, 1080)->text('Hello World');

$images = [
    'black red' => $base->color('black', 'red')->svg(),
    'black green' => $base->color('black', 'green')->avif(),
];

foreach ($placehold->batch($images) as $index => $image) {
    dd($image->detach());
}

mock(Client::class);
