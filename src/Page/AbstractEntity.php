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

use genug\Lib\EquivalentObjectInterface;
use genug\Lib\EquivalentObjectTrait;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
abstract class AbstractEntity implements EquivalentObjectInterface
{
    use EquivalentObjectTrait;

    public readonly AbstractId $id;
}
