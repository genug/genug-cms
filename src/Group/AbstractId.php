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

namespace genug\Group;

use genug\Lib\EquivalentStringableInterface;
use genug\Lib\EquivalentStringableTrait;

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
abstract class AbstractId implements EquivalentStringableInterface
{
    use EquivalentStringableTrait;
}
