<?php

declare(strict_types=1);

/*
 * This file is part of "genug".
 *
 * (c) David J. Schwarz
 * https://davidschwarz.eu
 *
 * License: MIT License
 */

namespace genug\Page;

use genug\Lib\Equatable;
use genug\Lib\IsEquatableSimply;

enum PageType: string implements Equatable
{
    use IsEquatableSimply;

    case HTML = 'html';
    case PHP = 'php';
    case PHTML = 'phtml';
}
