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

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
abstract class AbstractEntity
{
    public readonly AbstractId $id;

    final public function equals(mixed $entity): bool
    {
        return ($entity === $this);
    }
}
