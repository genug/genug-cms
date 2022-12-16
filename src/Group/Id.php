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

namespace genug\Group;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Id implements IdInterface
{
    use \genug\Lib\ValueObject\IdTrait;

    public const VALID_STRING_PATTERN = '#^[a-z0-9][a-z0-9_\-]*$#';

    public function equals(IdInterface $id): bool
    {
        return ($id instanceof self && (string) $this === (string) $id);
    }
}
