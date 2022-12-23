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

namespace genug\Lib;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */

trait EquivalentObjectTrait
{
    final public function equals(?object $object): bool
    {
        return ($object === $this);
    }
}
