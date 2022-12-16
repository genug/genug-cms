<?php

/*
 * This file is part of "genug".
 *
 * (c) David Schwarz / Ringsdorf
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

use PhpCsFixer\{
    Config,
    Finder
};

$fileHeaderComment = <<<'TXT'
This file is part of "genug".

(c) David Schwarz / Ringsdorf
https://davidschwarz.eu

License: MIT License
TXT;

return (new Config())
    ->setRules([
        'header_comment' => ['header' => $fileHeaderComment],
    ])
    ->setFinder(
        Finder::create()
        ->in(__DIR__.'/src')
        ->in(__DIR__.'/public')
        ->in(__DIR__.'/genug_user')
        ->append([__FILE__])
    )
    ->setCacheFile(__DIR__.'/.php-cs-fixer.cache')
;