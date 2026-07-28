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

use Stringable;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */

interface EquivalentStringableInterface extends Stringable
{
    public function equals(?Stringable $object): bool;
}
