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

/**
 *
 * @author David J. Schwarz <https://davidschwarz.eu/>
 * @license MIT License
 */
final class Id extends AbstractId
{
    use \genug\Lib\ValueObject\IdTrait;

    public const VALID_STRING_PATTERN = '#^[a-z0-9][a-z0-9_\-]*$#';
}
