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
use genug\Lib\ValueObject\IsId;
use Stringable;

use function Safe\preg_match;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class PageId implements Equatable, Stringable
{
    use IsId;

    public static function isValide(string $id): bool
    {
        return (bool) preg_match('#^(?:/|(?:/[a-z0-9][a-z0-9-]*)+)$#', $id);
    }
}
