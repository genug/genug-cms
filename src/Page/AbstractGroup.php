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

namespace genug\Page;

use Stringable;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
abstract class AbstractGroup implements Stringable
{
    final public function equals(mixed $group): bool
    {
        return ($this == $group && (string) $this === (string) $group);
    }
}
