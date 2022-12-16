<?php

use PhpCsFixer\{
    Config,
    Finder
};

return (new Config())
    ->setFinder(
        Finder::create()
        ->in(__DIR__.'/src')
        ->in(__DIR__.'/public')
        ->in(__DIR__.'/genug_user')
        ->append([__FILE__])
    )
    ->setCacheFile(__DIR__.'/.php-cs-fixer.cache')
;