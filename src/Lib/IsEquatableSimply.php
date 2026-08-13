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

namespace genug\Lib;

use function is_object;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 *
 * @phpstan-require-implements \genug\Lib\Equatable
 */
trait IsEquatableSimply
{
    final public function equals(mixed $other): bool
    {
        if (! is_object($other)) {
            return false;
        }

        return ($this == $other);
    }
}
