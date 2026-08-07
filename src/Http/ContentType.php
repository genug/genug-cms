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

namespace genug\Http;

use genug\Lib\Equatable;
use genug\Lib\EquatableSimpleObjectTrait;

enum ContentType: string implements Equatable
{
    use EquatableSimpleObjectTrait;

    case HTML = 'text/html';
}
