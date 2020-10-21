<?php

use Illuminate\Database\Query\Builder;

require __DIR__.'/../vendor/autoload.php';

/**
 * This file is pretty boring. It just created a Builder and calls the function
 * that we have overwritten. Look at ../scripts/ComposerScripts.php if you want
 * to see interesting bits.
 */

 /** @var \Illuminate\Database\Query\Builder */
$builder = (new ReflectionClass(Builder::class))->newInstanceWithoutConstructor();

var_dump($builder->cleanBindings([]));