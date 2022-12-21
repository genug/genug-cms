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

use Stringable;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */

interface EquivalentStringableInterface extends Stringable
{
    public function equals(?Stringable $object): bool;
}
