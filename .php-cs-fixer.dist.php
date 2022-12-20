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
        '@PSR12' => true,

        // Alias
        'no_alias_language_construct_call' => true,
        'no_mixed_echo_print' => true,

        // ...

        // Comment
        'header_comment' => [
            'header' => $fileHeaderComment
        ],
        'multiline_comment_opening_closing' => true,
        'no_empty_comment' => true,
        'single_line_comment_spacing' => true,
        'single_line_comment_style' => [
            'comment_types' => ['hash']
        ],

        // ...

        // Import
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true
        ],
        'no_unneeded_import_alias' => true,
        'no_unused_imports' => true,
        'ordered_imports' => [
            'imports_order' => [
                'class',
                'function',
                'const'
            ],
            'sort_algorithm' => 'alpha'
        ],
        'single_import_per_statement' => true,

        // ...

        // Whitespace
        // ...
        'no_extra_blank_lines' => [
            'tokens' => [
                'attribute',
                'break',
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'switch',
                'throw',
                'use',
                'use_trait'
            ]
        ],
        // ...

        // ...
    ])
    ->setFinder(
        Finder::create()
        ->in(__DIR__.'/src')
        ->in(__DIR__.'/public')
        ->in(__DIR__.'/tests')
    )
    ->setCacheFile(__DIR__.'/.php-cs-fixer.cache')
;