<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David Schwarz / Ringsdorf
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

/*
 * constants
 */

namespace genug\Setting
{
use function define;
use function defined;
use function dirname;
use function getcwd;

if (! defined(__NAMESPACE__ . '\USER_DIR')) {
        define(__NAMESPACE__ . '\USER_DIR', dirname(getcwd()) . '/genug_user');
    }

    if (! defined(__NAMESPACE__ . '\VIEW_DIR')) {
        define(__NAMESPACE__ . '\VIEW_DIR', namespace\USER_DIR . '/view');
    }

    if (! defined(__NAMESPACE__ . '\VIEW_INDEX_FILE')) {
        define(__NAMESPACE__ . '\VIEW_INDEX_FILE', namespace\VIEW_DIR . '/index.php');
    }
}
